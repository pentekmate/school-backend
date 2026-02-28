<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

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

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            foreach ($this->tasks ?? [] as $index => $task) {
                $this->validateTaskByType($validator, $task, $index);
            }

        });
    }

    private function validatePairing($validator, $task, $index)
    {
        if (count($task['pairing']['pairing_groups'] ?? []) > 8) {
            $validator->errors()->add(
                "tasks.$index.pairing.pairing_groups",
                'Maximum 8 párt alkothatsz.'
            );

            return;
        }

        if (empty($task['pairing']['pairing_groups'])) {
            $validator->errors()->add(
                "tasks.$index.pairing.pairing_groups",
                'Legalább egy pár megadása kötelező.'
            );

            return;
        }

        foreach ($task['pairing']['pairing_groups'] as $gIndex => $group) {

            $this->validateStringField(
                $validator,
                $group['pair_question'] ?? null,
                "tasks.$index.pairing.pairing_groups.$gIndex.pair_question",
                'A kérdés kötelező szöveg.'
            );

            if (strlen($group['pair_question']) > 130) {
                $validator->errors()->add(
                    "tasks.$index.pairing.pairing_groups.$gIndex.pair_question",
                    'A kérdés nem lehet hosszabb, mint 130 karakter.'
                );
            }

            $this->validateStringField(
                $validator,
                $group['pair_answer'] ?? null,
                "tasks.$index.pairing.pairing_groups.$gIndex.pair_answer",
                'A válasz kötelező szöveg.'
            );

            if (strlen($group['pair_answer']) > 130) {
                $validator->errors()->add(
                    "tasks.$index.pairing.pairing_groups.$gIndex.pair_answer",
                    'A válasz nem lehet hosszabb, mint 130 karakter.'
                );
            }
        }
    }

    private function validateGrouping($validator, $task, $index)
    {
        if (count($task['grouping']['groups'] ?? []) > 4) {
            $validator->errors()->add(
                "tasks.$index.grouping.groups",
                'Maximum 3 csoportot alkothatsz.'
            );

            return;
        }

        if (empty($task['grouping']['groups'])) {
            $validator->errors()->add(
                "tasks.$index.grouping.groups",
                'Legalább egy csoport megadása kötelező.'
            );

            return;
        }

        foreach ($task['grouping']['groups'] as $gIndex => $group) {
            $this->validateStringField(
                $validator,
                $group['name'] ?? null,
                "tasks.$index.grouping.groups.$gIndex.name",
                'A név kötelező szöveg'
            );

            if (strlen($group['name'] ?? null) > 30) {
                $validator->errors()->add(
                    "tasks.$index.grouping.groups.$gIndex.name",
                    'A megadott csoportnév túlhosszú.'
                );
            }

            if (count($group['items']) > 4) {
                $validator->errors()->add(
                    "tasks.$index.grouping.groups.$gIndex.items",
                    'Maximum 5 elem lehet egy csoportban.'
                );
            }
            if (empty($group['items'])) {
                $validator->errors()->add(
                    "tasks.$index.grouping.groups.$gIndex.items",
                    'Üres csoportot nem hozhatsz létre.'
                );
            }

            foreach ($group['items'] as $gItemIndex => $groupItem) {
                $this->validateStringField(
                    $validator,
                    $groupItem['name'] ?? null,
                    "tasks.$index.grouping.groups.{$group['name']}.$gItemIndex.name",
                    'A csoport elemei kötelező szöveg.'
                );

                if (strlen($groupItem['name'] ?? null) > 30) {
                    $validator->errors()->add(
                        "tasks.$index.grouping.groups.{$group['name']}.$gItemIndex.name",
                        'A csoport elem nem lehet hosszabb 30 karakternél.'
                    );
                }
            }
        }
    }

    private function validateShortAnswer($validator, $task, $index)
    {
        if (count($task['short_answer']['questions'] ?? []) > 18) {
            $validator->errors()->add(
                "tasks.$index.short_answer.questions",
                'Maximum 18 kérdést alkothatsz.'
            );

            return;
        }

        if (empty($task['short_answer']['questions'])) {
            $validator->errors()->add(
                "tasks.$index.short_answer.questions",
                'Legalább egy kérdés megadása kötelező.'
            );

            return;
        }

        foreach ($task['short_answer']['questions'] as $gIndex => $shortAnswer) {

            $this->validateStringField(
                $validator,
                $shortAnswer['question'] ?? null,
                "tasks.$index.short_answer.questions.$gIndex.question",
                'A kérdés kötelező szöveg.'
            );

            if (strlen($shortAnswer['question']) > 150) {
                $validator->errors()->add(
                    "tasks.$index.short_answer.questions.$gIndex.question",
                    'A kérdés nem lehet hosszabb, mint 150 karakter.'
                );
            }

            $this->validateStringField(
                $validator,
                $shortAnswer['answer'] ?? null,
                "tasks.$index.short_answer.questions.$gIndex.answer",
                'A válasz kötelező szöveg.'
            );

            if (strlen($shortAnswer['answer']) > 50) {
                $validator->errors()->add(
                    "tasks.$index.short_answer.questions.$gIndex.answer",
                    'A válasz nem lehet hosszabb, mint 50 karakter.'
                );
            }
        }
    }

    private function validateAssignment($validator, $task, $index)
    {
        $this->validateStringField(
            $validator,
            $task['assignment']['imgURL'] ?? null,
            "tasks.$index.assignment.imageURL",
            'A kép megadása kötelező.'
        );
        if (empty($task['assignment']['coordinatesAndAnswers'])) {
            $validator->errors()->add(
                "tasks.$index.assignment.coordinatesAndAnswers",
                'Legalább 1 kordináta megadása kötelező.'
            );
        }

        if (count($task['assignment']['coordinatesAndAnswers']) > 10) {
            $validator->errors()->add(
                "tasks.$index.assignment.coordinatesAndAnswers",
                'Maximum 10 kordinátát adhatsz meg.'
            );
        }

        foreach ($task['assignment']['coordinatesAndAnswers'] as $cIndex => $coordinate) {
           
            $this->validateStringField(
                $validator,
                $coordinate['coordinate'] ?? null,
                "tasks.$index.assignment.coordinatesAndAnswers.$cIndex.coordinate",
                'A kordináta megadása kötelező.'
            );
            if (count($coordinate['answers']) > 2) {
                $validator->errors()->add(
                    "tasks.$index.assignment.coordinatesAndAnswers.$cIndex.answers",
                    '1 kordiánához maximum 2 válasz adható meg.'
                );
            }
            $answers = $coordinate['answers'] ?? [];
            $correctCount = collect($answers)
                ->where('isCorrect', true)
                ->count();

            if ($correctCount < 1) {
                $validator->errors()->add(
                    "tasks.$index.coordinatesAndAnswers.$cIndex.answers",
                    'Legalább 1 helyes választ meg kell adni.'
                );
            }

            if ($correctCount > 1) {
                $validator->errors()->add(
                    "tasks.$index.coordinatesAndAnswers.$cIndex.answers",
                    'Maximum 1 helyes válasz adható meg.'
                );
            }
            foreach ($coordinate['answers'] as $aIndex => $answer) {
                $this->validateStringField(
                    $validator,
                    $answer['answer'] ?? null,
                    "tasks.$index.assignment.coordinatesAndAnswers.$cIndex.coordinate.answers$aIndex.answer",
                    'A válasz megadása kötelező.'
                );
            }
        }
    }

    private function validateTaskByType($validator, $task, $index)
    {
        return match ($task['task_type_id']) {
            1 => $this->validateGrouping($validator, $task, $index),
            2 => $this->validatePairing($validator, $task, $index),
            3 => $this->validateShortAnswer($validator, $task, $index),
            4 => $this->validateAssignment($validator, $task, $index),
            default => null,
        };
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validációs hiba történt.',
            'errors' => $validator->errors(),
        ], 422));
    }

    private function validateStringField($validator, $value, $path, $message)
    {
        if (! is_string($value) || trim($value) === '') {
            $validator->errors()->add($path, $message);
        }
    }
}
