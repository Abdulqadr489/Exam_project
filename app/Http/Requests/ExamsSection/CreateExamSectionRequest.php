<?php

namespace App\Http\Requests\ExamsSection;

use Illuminate\Foundation\Http\FormRequest;

class CreateExamSectionRequest extends FormRequest
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
            'exam_id' => ['required','exists:exams,id'],
            'name' => ['nullable', 'string'],
            'order' => ['nullable', 'integer', 'min:1'],
            'instructions' => ['nullable', 'string'],

        ];
    }
}
