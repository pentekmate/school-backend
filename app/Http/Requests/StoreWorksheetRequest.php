<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorksheetRequest extends FormRequest
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
            'title' => 'required|string|max:255',

            'user_id' => 'required|integer|exists:users,id',
            'subject_id' => 'required|integer|exists:subjects,id',
            'classroom_id' => 'required|integer|exists:classrooms,id',

            'lifetime_minutes' => 'required|integer|min:1',
            'max_time_to_resolve_minutes' => 'required|integer|min:1',
            'grade' => 'required|integer|min:1|max:12',
            'is_public' => 'required|boolean',

            'tasks' => 'required|array|min:1',

            'tasks.*.task_title' => 'required|string|max:255',
            'tasks.*.task_description' => 'nullable|string',
            'tasks.*.task_type_id' => 'required|integer|exists:task_types,id',
            'tasks.*.feedback' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'A feladatlap cím megadása kötelező.',

            'tasks.required' => 'Legalább egy feladatot hozzá kell adni.',

            'tasks.*.task_title.required' => 'Minden feladatnak kell címet adni.',

            'tasks.*.assignment.imgURL.required_if' => 'Az assignment típusú feladatnál kötelező a kép.',
        ];
    }

    // protected function failedValidation(Validator $validator)
    // {
    //     throw new HttpResponseException(response()->json([
    //         'success' => false,
    //         'message' => 'Validációs hiba történt.',
    //         'errors' => $validator->errors(),
    //     ], 422));
    // }
}
