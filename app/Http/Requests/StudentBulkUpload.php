<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentBulkUpload extends FormRequest
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
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
            'classroom_id' => 'required|exists:classrooms,id',
            'user_id' => 'required',
        ];

    }

    public function messages(): array
    {
        return [
            'file.required' => 'A fájl megadása kötelező.',

            'file.mimes:xlsx,xls,csv' => 'A fájl típus nem megfelelő.',

            'classroom_id.required' => 'Hiányzó adatok.',

            'user_id' => 'Hiányzó adatok.',
        ];
    }
}
