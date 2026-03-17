<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class TaskResultResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        $solutionId = $request->current_solution_id;

        return [
            'id' => $this->id,
            'type_id' => $this->task_type_id,
            'title' => $this->title,
            'data' => $this->prepareSpecificData($solutionId),
        ];
    }

    protected function prepareSpecificData($solutionId)
    {

        return match ($this->task_type_id) {
            1 => $this->formatGroupingData($solutionId),
            2 => $this->formatPairingData($solutionId),
            3 => $this->formatShortAnswerData($solutionId),
            4 => $this->formatAssignmentData($solutionId),
            default => [],
        };
    }

    protected function formatGroupingData($solutionId)
    {
        $groups = DB::table('groups')
            ->where('task_grouping_id', function ($query) {
                $query->select('id')
                    ->from('task_groupings')
                    ->where('task_id', $this->id);
            })
            ->get();

        return $groups->map(function ($group) use ($solutionId) {

            $allCorrectItems = DB::table('group_items')
                ->where('group_id', $group->id)
                ->pluck('name');

            $studentAnswers = DB::table('grouping_user_answers')
                ->join('group_items', 'grouping_user_answers.group_item_id', '=', 'group_items.id')
                ->where('grouping_user_answers.worksheet_solution_id', $solutionId)
                ->where('grouping_user_answers.group_id', $group->id)
                ->select([
                    'group_items.name',

                ])
                ->get();

            return [
                'group_id' => $group->id,
                'group_name' => $group->name,
                'correct_solution_list' => $allCorrectItems, // Referencia a tanárnak
                'student_answers' => $studentAnswers, // Mit rakott ide a diák és jó-e
            ];
        });
    }

    protected function formatPairingData($solutionId)
    {
        $questions = DB::table('pair_questions')
            ->join('pair_groups', 'pair_questions.pair_group_id', '=', 'pair_groups.id')
            ->where('task_pair_id', function ($query) {
                $query->select('id')
                    ->from('task_pairs')
                    ->where('task_id', $this->id);
            })
            ->get();

        return $questions->map(function ($question) use ($solutionId) {
            $correctAnswer = DB::table('pair_answers')
                ->where('pair_group_id', $question->pair_group_id)
                ->value('answer');

            $studentAnswers = DB::table('pairing_user_answers')
                ->join('pair_answers', 'pairing_user_answers.pair_answer_id', '=', 'pair_answers.id')
                ->where('pairing_user_answers.worksheet_solution_id', $solutionId)
                ->where('pairing_user_answers.pair_question_id', $question->id)
                ->value('pair_answers.answer');

            return [
                'question' => $question->question,
                'correctAnswer' => $correctAnswer,
                'userAnswer' => $studentAnswers,
            ];
        });

    }

    protected function formatShortAnswerData($solutionId)
    {
        $questions = DB::table('task_short_answer_questions')
            ->join('task_short_answers', 'task_short_answer_questions.task_short_answers_id', '=', 'task_short_answers.id')
            ->where('task_short_answers.task_id', $this->id)
            ->select('task_short_answer_questions.id', 'task_short_answer_questions.question')
            ->get();

        return $questions->map(function ($question) use ($solutionId) {
            $correctAnswer = DB::table('task_short_answer_answers')
                ->where('task_short_answer_question_id', $question->id)
                ->value('answer');

            $userAnswer = DB::table('short_answer_user_answers')
                ->where('worksheet_solution_id', $solutionId)
                ->where('task_short_answer_question_id', $question->id)
                ->value('user_answer');

            return [
                'question' => $question->question,
                'correctAnswer' => $correctAnswer,
                'userAnswer' => $userAnswer,
            ];
        });

    }

    protected function formatAssignmentData($solutionId)
    {
        $images = DB::table('task_assignment_images')
            ->join('task_assignments', 'task_assignments.id', '=', 'task_assignment_images.task_assignment_id')
            ->where('task_assignments.id', $this->id)
            ->select('task_assignment_images.id', 'task_assignment_images.imgURL')
            ->get();

        return $images->map(function ($image) use ($solutionId) {
            $coordinates = DB::table('task_assignment_coordinates')
                ->where('task_assignment_image_id', $image->id)
                ->select('id', 'coordinate')
                ->get();

            $coordinatesAndAnswers = $coordinates->map(function ($coord) use ($solutionId) {

                $correctAnswer = DB::table('task_assignment_answers')
                    ->where('task_assignment_coordinate_id', $coord->id)
                    ->where('task_assignment_answers.isCorrect', 1)
                    ->value('answer');

                $userAnswer = DB::table('assignment_user_answers')
                    ->join('task_assignment_answers', 'assignment_user_answers.task_assignment_answer_id', '=', 'task_assignment_answers.id')
                    ->where('assignment_user_answers.worksheet_solution_id', $solutionId)
                    ->where('assignment_user_answers.task_assignment_coordinate_id', $coord->id)
                    ->value('task_assignment_answers.answer');

                return [
                    'coordinate' => $coord->coordinate,
                    'correctAnswer' => $correctAnswer,
                    'userAnswer' => $userAnswer ?? 'Nincs válasz',
                ];
            });

            return [
                'imgURL' => $image->imgURL,
                'coordinatesAndAnswers' => $coordinatesAndAnswers,
            ];
        });

    }
}
