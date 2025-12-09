<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait CommonUsage
{
    protected static array $joinedTables = [];

    /**
     * Search through model attributes and relationships.
     */
    public function scopeSearch(
        Builder $query,
        ?string $searchTerm,
        array $searchableColumns = [],
        array $relations = []
    ): Builder {
        if (empty($searchTerm)) {
            return $query;
        }

        $table = $query->getModel()->getTable();

        return $query->where(function ($query) use ($searchTerm, $searchableColumns, $relations, $table) {
            foreach ($searchableColumns as $column) {
                if (strpos($column, '.') === false) {
                    $column = "{$table}.{$column}";
                }

                [$tbl, $col] = explode('.', $column);
                if (self::hasFulltextIndex($tbl, $col)) {
                    $query->orWhereFullText($column, $searchTerm);
                } else {
                    $query->orWhere($column, 'like', "%{$searchTerm}%");
                }
            }

            foreach ($relations as $relation => $columns) {
                $query->orWhereHas($relation, function ($q) use ($searchTerm, $columns) {
                    $relatedTable = $q->getModel()->getTable();
                    $q->where(function ($q2) use ($searchTerm, $columns, $relatedTable) {
                        foreach ($columns as $col) {
                            if (self::hasFulltextIndex($relatedTable, $col)) {
                                $q2->orWhereFullText($col, $searchTerm);
                            } else {
                                $q2->orWhere($col, 'like', "%{$searchTerm}%");
                            }
                        }
                    });
                });
            }
        });
    }

    protected static function hasFulltextIndex(string $table, string $column): bool
    {
        try {
            $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Column_name = ? AND Index_type = 'FULLTEXT'", [$column]);
            return !empty($indexes);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Dynamically sort by model or relationship column.
     */
    public function scopeSortBy(Builder $query, string $sortColumn, string $sortDirection = 'asc'): Builder
    {
        $model = $query->getModel();

        // Basic column sort
        if (strpos($sortColumn, '.') === false) {
            return $query->orderBy($sortColumn, $sortDirection);
        }

        // Handle relation sort
        $parts = explode('.', $sortColumn);
        $column = array_pop($parts);
        $relationChain = $parts;

        $previousTable = $model->getTable();
        $aliasCounter = 0;

        foreach ($relationChain as $relationName) {
            if (!method_exists($model, $relationName)) {
                throw new \Exception("Relation {$relationName} not found on " . get_class($model));
            }

            /** @var \Illuminate\Database\Eloquent\Relations\Relation $relation */
            $relation = $model->$relationName();
            $related = $relation->getRelated();
            $relatedTable = $related->getTable();

            // Reuse same alias name for readability
            $alias = $relationName . '_join';

            // Prevent duplicate joins
            if (in_array($alias, static::$joinedTables)) {
                $model = $related;
                $previousTable = $alias;
                continue;
            }

            static::$joinedTables[] = $alias;

            if ($relation instanceof BelongsTo) {
                $query->leftJoin("{$relatedTable} as {$alias}",
                    "{$previousTable}." . $relation->getForeignKeyName(),
                    '=',
                    "{$alias}." . $related->getKeyName()
                );
            } else {
                $query->leftJoin("{$relatedTable} as {$alias}",
                    "{$alias}." . $relation->getForeignKeyName(),
                    '=',
                    "{$previousTable}." . $model->getKeyName()
                );
            }

            $model = $related;
            $previousTable = $alias;
        }

        return $query->orderBy("{$previousTable}.{$column}", $sortDirection);
    }
}
