<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;

class SubmitWorksheetRequest extends FormRequest
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
            'worksheet_id' => 'required|integer|exists:users,id',
            'student_id' => 'required|integer|exists:students,id',
            'tasks' => 'array|min:1|required',
            'tasks.*.task_type_id' => 'required|integer|in:1,2,3,4',
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

    private function validateTaskByType($validator, $task, $index)
    {
        return match ($task['task_type_id'] ?? null) {
            1 => $this->validateGrouping($validator, $task, $index),
            2 => $this->validatePairing($validator, $task, $index),
            3 => $this->validateShortAnswer($validator, $task, $index),
            4 => $this->validateAssignment($validator, $task, $index),
            default => null,
        };
    }

    private function validateGrouping($validator, $task, $index)
    {
        $submittedTaskId = $task['task_id'] ?? 0;
        $path = "tasks.$index.solutions";

        if (! isset($task['task_id'])) {
            $validator->errors()->add(
                "tasks.$index.task_id",
                'Hiányzó feladat azonosító.'
            );
        }
        if (! isset($task['solutions'])) {
            $validator->errors()->add(
                "tasks.$index.task_id",
                'Hiányzó feladat kitöltések.'
            );
        }
        foreach (($task['solutions'] ?? []) as $solIndex => $sol) {

            $isValidGroup = DB::table('groups')
                ->join('task_groupings', 'task_groupings.id', '=', 'groups.task_grouping_id')
                ->where('groups.id', $sol['group_id'] ?? 0)
                ->where('task_groupings.task_id', $submittedTaskId)
                ->exists();

            if (! $isValidGroup) {
                $validator->errors()->add($path, 'A megadott adatok érvénytelenek.');
                break;
            }
            foreach ($sol['group_item_ids'] as $item) {
                $isValidItem = DB::table('group_items')
                    ->join('groups', 'groups.id', '=', 'group_items.group_id')
                    ->join('task_groupings', 'task_groupings.id', '=', 'groups.task_grouping_id')
                    ->where('group_items.id', $item ?? 0)
                    ->where('task_groupings.task_id', $submittedTaskId)
                    ->exists();

                if (! $isValidItem) {
                    $validator->errors()->add($path, 'A megadott adatok érvénytelenek.');
                    break;
                }
            }

        }

    }

    protected function validatePairing($validator, $task, $index)
    {
        $path = "tasks.$index.solutions";
        $submittedTaskId = $task['task_id'] ?? 0;

        if (! isset($task['task_id']) || ! isset($task['solutions'])) {
            $validator->errors()->add("tasks.$index.task_id", 'Hiányzó feladat adatok.');

            return;
        }

        foreach (($task['solutions'] ?? []) as $solIndex => $sol) {

            $isValidQuestion = DB::table('pair_questions')
                ->join('pair_groups', 'pair_questions.pair_group_id', '=', 'pair_groups.id')
                ->join('task_pairs', 'pair_groups.task_pair_id', '=', 'task_pairs.id')
                ->where('pair_questions.id', $sol['question_id'] ?? 0)
                ->where('task_pairs.task_id', $submittedTaskId)
                ->exists();

            $isValidAnswer = DB::table('pair_answers')
                ->join('pair_groups', 'pair_answers.pair_group_id', '=', 'pair_groups.id')
                ->join('task_pairs', 'pair_groups.task_pair_id', '=', 'task_pairs.id')
                ->where('pair_answers.id', $sol['answer_id'] ?? 0)
                ->where('task_pairs.task_id', $submittedTaskId)
                ->exists();

            if (! $isValidQuestion || ! $isValidAnswer) {

                $validator->errors()->add($path, 'A megadott adatok érvénytelenek.');

                break;
            }
        }
    }

    protected function validateShortAnswer($validator, $task, $index)
    {
        $path = "tasks.$index.solutions";
        $submittedTaskId = $task['task_id'] ?? 0;
        if (! isset($task['task_id'])) {
            $validator->errors()->add(
                "tasks.$index.task_id",
                'Hiányzó feladat azonosító.'
            );
        }
        if (! isset($task['solutions'])) {
            $validator->errors()->add(
                "tasks.$index.task_id",
                'Hiányzó feladat kitöltések.'
            );
        }
        foreach (($task['solutions'] ?? []) as $solIndex => $sol) {

            $isValidQuestion = DB::table('task_short_answer_questions')
                ->join('task_short_answers', 'task_short_answer_questions.task_short_answers_id', '=', 'task_short_answers.id')
                ->where('task_short_answer_questions.id', $sol['question_id'] ?? 0)
                ->where('task_short_answers.task_id', $submittedTaskId)
                ->exists();

            $answerIsValid = ! isset($sol['answer']) || is_string($sol['answer']);

            if (! $isValidQuestion || ! $answerIsValid) {

                $validator->errors()->add($path, 'A megadott adatok érvénytelenek.');

                break;
            }
        }
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validációs hiba történt.',
            'errors' => $validator->errors(),
        ], 422));
    }

    protected function validateAssignment($validator, $task, $index)
    {
        $path = "tasks.$index.solutions";
        $submittedTaskId = $task['task_id'] ?? 0;

        if (! isset($task['task_id']) || ! isset($task['solutions'])) {
            $validator->errors()->add("tasks.$index.task_id", 'Hiányzó feladat adatok.');

            return;
        }

        foreach (($task['solutions'] ?? []) as $solIndex => $sol) {

            $imgExists = DB::table('task_assignment_images')
                ->join('task_assignments', 'task_assignment_images.task_assignment_id', '=', 'task_assignments.id')
                ->where('task_assignment_images.id', $sol['img_id'] ?? 0)
                ->where('task_assignments.task_id', $submittedTaskId)
                ->exists();

            if (! $imgExists) {
                $validator->errors()->add($path, 'A megadott adatok érvénytelenek.');
                break;
            }

            if (! isset($sol['answers']) || ! is_array($sol['answers'])) {

                continue;
            }

            foreach ($sol['answers'] as $ans) {
                // $coordinateExists = DB::table('task_assignment_coordinates')
                //     ->where('id', $ans['coordinate_id'] ?? 0)
                //     ->exists();

                $coordinateExists = DB::table('task_assignment_coordinates')
                    ->join('task_assignment_images', 'task_assignment_images.id', '=', 'task_assignment_coordinates.task_assignment_image_id')
                    ->join('task_assignments', 'task_assignment_images.task_assignment_id', '=', 'task_assignments.id')
                    ->where('task_assignment_images.id', $ans['coordinate_id'] ?? 0)
                    ->where('task_assignments.task_id', $submittedTaskId)
                    ->exists();

                $answerExists = DB::table('task_assignment_answers')
                    ->where('id', $ans['answer_id'] ?? 0)
                    ->exists();

                if (! $coordinateExists || ! $answerExists) {
                    $validator->errors()->add($path, 'A megadott adatok érvénytelenek.');
                    break 2;
                }
            }
        }
    }

    public function messages(): array
    {
        return [
            'worksheet_id.required' => 'A feladatlap azonosítója kötelező.',

            'student_id.required' => 'A diák azonosítója kötelező.',

            'tasks.required' => 'A feladatok megadása kötelező.',

            'tasks.*.task_type_id' => 'Hiányzó feladat típus azonosító.',
        ];
    }
}
