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

        $solutions = $task['solutions'] ?? [];
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
        $groups = collect($solutions)->pluck('group_id')->filter()->unique()->toArray();

        $validGroupCount = DB::table('groups')
            ->join('task_groupings', 'task_groupings.id', '=', 'groups.task_grouping_id')
            ->whereIn('groups.id', $groups)
            ->where('task_groupings.task_id', $submittedTaskId)
            ->count();
        if (count($groups) !== $validGroupCount) {
            $validator->errors()->add('Hibás adatok', $path);

            return;
        }

        foreach (($task['solutions'] ?? []) as $solIndex => $sol) {
            $group_items = $sol['group_item_ids'];

            if (empty($group_items)) {
                continue;
            }

            $validIds = DB::table('group_items')
                ->join('groups', 'groups.id', '=', 'group_items.group_id')
                ->join('task_groupings', 'task_groupings.id', '=', 'groups.task_grouping_id')
                ->whereIn('group_items.id', $group_items)
                ->where('task_groupings.task_id', $submittedTaskId)
                ->count();

            if (count($group_items) !== $validIds) {
                $validator->errors()->add($path, 'A megadott adatok érvénytelenek.');
                break;
            }

        }

    }

    protected function validatePairing($validator, $task, $index)
    {
        $path = "tasks.$index.solutions";
        $submittedTaskId = $task['task_id'] ?? 0;
        $solutions = $task['solutions'] ?? [];

        if (! isset($task['task_id']) || ! isset($task['solutions'])) {
            $validator->errors()->add("tasks.$index.task_id", 'Hiányzó feladat adatok.');

            return;
        }

        $submittedQuestions = collect($solutions)->pluck('question_id')->filter()->unique()->toArray();
        $submittedAnswers = collect($solutions)->pluck('answer_id')->filter()->unique()->toArray();

        $validQuestionCount = DB::table('pair_questions')
            ->join('pair_groups', 'pair_questions.pair_group_id', '=', 'pair_groups.id')
            ->join('task_pairs', 'pair_groups.task_pair_id', '=', 'task_pairs.id')
            ->whereIn('pair_questions.id', $submittedQuestions)
            ->where('task_pairs.task_id', $submittedTaskId)
            ->count();
        $validAnswerCount = DB::table('pair_answers')
            ->join('pair_groups', 'pair_answers.pair_group_id', '=', 'pair_groups.id')
            ->join('task_pairs', 'pair_groups.task_pair_id', '=', 'task_pairs.id')
            ->whereIn('pair_answers.id', $submittedAnswers)
            ->where('task_pairs.task_id', $submittedTaskId)
            ->count();

        if (count($submittedAnswers) !== $validAnswerCount || count($submittedQuestions) !== $validQuestionCount) {
            $validator->errors()->add("tasks.$index.solutions", 'Érvénytelen adatok!');

            return;
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

        $solutions = $task['solutions'] ?? [];

        $submittedQuestions = collect($solutions)->pluck('question_id')->filter()->unique()->toArray();

        $validQuestionCount = DB::table('task_short_answer_questions')
            ->join('task_short_answers', 'task_short_answer_questions.task_short_answers_id', '=', 'task_short_answers.id')
            ->whereIn('task_short_answer_questions.id', $submittedQuestions)
            ->where('task_short_answers.task_id', $submittedTaskId)
            ->count();

        if ($validQuestionCount !== count($submittedQuestions)) {
            $validator->errors()->add("tasks.$index.solutions", 'Érvénytelen kérdés azonosítók.');

            return;
        }

        foreach (($task['solutions'] ?? []) as $solIndex => $sol) {

            $answerIsValid = ! isset($sol['answer']) || is_string($sol['answer']);

            if (! $answerIsValid) {

                $validator->errors()->add($path, 'A megadott adatok érvénytelenek.');

                break;
            }
        }
    }

    protected function validateAssignment($validator, $task, $index)
    {
        $path = "tasks.$index.solutions";
        $submittedTaskId = $task['task_id'] ?? 0;
        $solutions = $task['solutions'] ?? [];

        foreach ($solutions as $solIndex => $sol) {

            $imgId = $sol['img_id'] ?? 0;
            $imgExists = DB::table('task_assignment_images')
                ->join('task_assignments', 'task_assignment_images.task_assignment_id', '=', 'task_assignments.id')
                ->where('task_assignment_images.id', $imgId)
                ->where('task_assignments.task_id', $submittedTaskId)
                ->exists();

            if (! $imgExists) {
                $validator->errors()->add($path, 'Érvénytelen kép azonosító.');

                continue;
            }

            $answers = $sol['answers'] ?? [];
            if (empty($answers)) {
                continue;
            }

            $submittedCoordIds = collect($answers)->pluck('coordinate_id')->unique()->toArray();
            $submittedAnswerIds = collect($answers)->pluck('answer_id')->unique()->toArray();

            $validCoordsCount = DB::table('task_assignment_coordinates')
                ->whereIn('id', $submittedCoordIds)
                ->where('task_assignment_image_id', $imgId)
                ->count();

            $validAnswersCount = DB::table('task_assignment_answers')
                ->join('task_assignment_coordinates', 'task_assignment_answers.task_assignment_coordinate_id', '=', 'task_assignment_coordinates.id')
                ->whereIn('task_assignment_answers.id', $submittedAnswerIds)
                ->where('task_assignment_coordinates.task_assignment_image_id', $imgId)
                ->count();

            if ($validCoordsCount !== count($submittedCoordIds) || $validAnswersCount !== count($submittedAnswerIds)) {
                $validator->errors()->add($path, 'A megadott koordináták vagy válaszok érvénytelenek.');
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
