<?php

namespace App\Http\Requests\ExamTypes;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateExamTypeRequest extends FormRequest
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
        $type = (new CreateExamTypeRequest())->rules();

        $id = $this->route('exam_type')->id ?? null;

        $type['code'] = [
            'nullable',
            'string',
            'max:255',
            Rule::unique('exam_types', 'code')->ignore($id),
        ];

        return $type;
    }
}
