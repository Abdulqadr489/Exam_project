<?php

namespace App\Http\Requests\Questions;

use Illuminate\Foundation\Http\FormRequest;

class CreateQuestionRequest extends FormRequest
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
            "question_type" => ["required", "string", "max:255"],
            "question_text" => ["required", "string", "max:255"],
            "media_url"     => 'nullable|file|mimes:mp3,wav,m4a,ogg|max:20480', // 20MB
            "max_score" => ["required", "integer", "min:1", "max:100"],
            "meta"=>["nullable", "string", "max:255"],
        ];
    }
}
