<?php

namespace App\Http\Requests\BaseListRequests;

use Illuminate\Foundation\Http\FormRequest;

class BaseListRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search' =>['nullable','string','max:255'],
            'sort_by' =>['nullable','string','max:255'],
            'sort_dir'=>['nullable','string','max:255'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page'     => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function listParams(): array
    {
        return [
            'search'   => $this->input('search',  ),
            'sort_by'  => $this->input('sort_by'),
            'sort_dir' => $this->input('sort_dir', 'asc'),
            'per_page' => $this->input('per_page', 10),
        ];
    }

}
