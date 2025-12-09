<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;
use App\Helpers\ApiResponseHelper;
use Illuminate\Support\Facades\Cache;

class ListServices
{
    protected Builder $query;
    protected array $relations = [];
    protected array $searchableColumns = [];
    protected array $fieldMap = [];
    protected array $sortMap = [];
    protected int $perPage = 10;
    protected string $sortBy = 'id';
    protected string $sortDir = 'asc';
    protected ?string $searchTerm = null;
    protected ?LengthAwarePaginator $paginated = null;

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    public function withRelations(array $relations): self
    {
        $this->relations = $relations;
        return $this;
    }

    public function searchable(array $columns): self
    {
        $this->searchableColumns = $columns;
        return $this;
    }

    public function fields(array $fieldMap): self
    {
        $this->fieldMap = $fieldMap;
        return $this;
    }

    public function sortMap(array $map): self
    {
        $this->sortMap = $map;
        return $this;
    }

    public function paginate(int $perPage = 10): self
    {
        $this->perPage = $perPage;
        return $this;
    }

    public function sort(?string $sortBy, ?string $sortDir = 'asc'): self
    {
        $this->sortBy = $sortBy ?: 'id';
        $this->sortDir = in_array(strtolower($sortDir), ['asc', 'desc']) ? $sortDir : 'asc';
        return $this;
    }

    public function search(?string $searchTerm): self
    {
        $this->searchTerm = $searchTerm;
        return $this;
    }

    /**
     * Build and paginate query
     */
    public function get(): LengthAwarePaginator
    {
        if ($this->paginated) {
            return $this->paginated;
        }

        if ($this->relations && ($this->searchTerm || $this->sortMap || $this->fieldMap)) {
            $this->query->with(array_keys($this->relations));
        }

        if ($this->searchTerm && method_exists($this->query->getModel(), 'scopeSearch')) {
            $this->query->search($this->searchTerm, $this->searchableColumns, $this->relations);
        }

        $sortColumn = $this->sortMap[$this->sortBy] ?? $this->sortBy;

        if (!Schema::hasColumn($this->query->getModel()->getTable(), $this->sortBy)
            && !isset($this->sortMap[$this->sortBy])
            && strpos($sortColumn, '.') === false
        ) {
            throw new \InvalidArgumentException("Invalid sort column: {$this->sortBy}");
        }

        if (method_exists($this->query->getModel(), 'scopeSortBy')) {
            $this->query->sortBy($sortColumn, $this->sortDir);
        } else {
            $this->query->orderBy($sortColumn, $this->sortDir);
        }

        $cacheKey = 'list:' . md5(json_encode([
                'sql' => $this->query->toSql(),
                'bindings' => $this->query->getBindings(),
                'page' => request('page', 1),
                'perPage' => $this->perPage
            ]));

        return $this->paginated = Cache::remember($cacheKey, 60, function () {
            return $this->query->paginate($this->perPage);
        });
    }

    public function toApiResponse()
    {
        return ApiResponseHelper::formatPaginatedFields($this->get(), $this->fieldMap);
    }
}
