<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentUpload extends FormRequest
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
            'classroom_id' => 'required|exists:classrooms,id',
            'user_id' => 'required',
            'name'=>'required|string|max:30'
        ];
    }


     public function messages(): array
    {
        return [
            'name.required'=>'A diák nevét kötelező megadni.',

            'name.string'=>'A diák nevének típusa nem megfelelő.',

            'name.max:30'=>'A diák neve túl hosszú',

            'classroom_id.required' => 'Hiányzó adatok.',

            'user_id' => 'Hiányzó adatok.',
        ];
    }
}
