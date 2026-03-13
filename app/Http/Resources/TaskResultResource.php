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
            3 => $this->formatShortAnswerData(),
            4 => $this->formatAssignmentData(),
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
    protected function formatPairingData($solutionId){
        $questions = DB::table('pair_questions')
        ->join('pair_groups','pair_questions.pair_group_id','=','pair_groups.id')
            ->where('task_pair_id', function ($query) {
                $query->select('id')
                    ->from('task_pairs')
                    ->where('task_id', $this->id);
            })
            ->get();
          return $questions->map(function ($question) use ($solutionId) {
            $correctAnswer = DB::table('pair_answers')
                ->where('pair_group_id', $question->pair_group_id)
                ->pluck('answer');

            
            $studentAnswers = DB::table('pairing_user_answers')
                ->join('pair_answers', 'pairing_user_answers.pair_answer_id', '=', 'pair_answers.id')
                ->where('pairing_user_answers.worksheet_solution_id', $solutionId)
                ->where('pairing_user_answers.pair_question_id', $question->id)
                ->select([
                    'pair_answers.answer',

                ])
                ->get();

                return [
                    'question'=>$question->question,
                    'correctAnswer'=>$correctAnswer,
                    'userAnswer'=>$studentAnswers
                ];
          });
      
    }
}
