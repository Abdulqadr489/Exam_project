<?php

namespace App\Http\Requests\ExamTypes;

use Illuminate\Foundation\Http\FormRequest;

class CreateExamTypeRequest extends FormRequest
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
            'name' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'code' => ['nullable', 'string','unique:exam_types,code'],
            'scoring_strategy'=>['nullable', 'string'],
            'meta' => 'nullable|array',
        ];
    }
}
