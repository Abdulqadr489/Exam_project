<?php

namespace App\Http\Requests\Exams;

use Illuminate\Foundation\Http\FormRequest;

class CreateExamRequest extends FormRequest
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
            'exam_type_id'=>['required','exists:exam_types,id'],
            'title'=>['required','string'],
            'description'=>['nullable','string'],
            'duration'=>['integer','numeric'],
            'price_iqd'=>['integer','numeric'],
        ];
    }
}
