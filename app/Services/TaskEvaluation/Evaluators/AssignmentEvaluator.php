<?php

namespace App\Services\TaskEvaluation\Evaluators;

use App\Models\Assignment_user_answer;
use Illuminate\Support\Facades\DB;

class AssignmentEvaluator
{
    /**
     * Short answer típusú task kiértékelése
     *
     * @return array
     */
    public function evaluate(int $taskId, array $solutions, int $solutionId): int
    {
        $score = 0;

        foreach ($solutions as $sol) {
            foreach ($sol['answers'] as $coordinateAndAnswer) {
                $isCorrect = DB::table('task_assignment_answers')
                    ->where('id', $coordinateAndAnswer['answer_id'])
                    ->where('isCorrect', 1)
                    ->exists();

                if ($isCorrect) {
                    $score++;
                }

                Assignment_user_answer::create([
                    'worksheet_solution_id' => $solutionId,
                    'task_assignment_image_id' => $sol['img_id'],
                    'task_assignment_coordinate_id' => $coordinateAndAnswer['coordinate_id'],
                    'task_assignment_answer_id' => $coordinateAndAnswer['answer_id'],
                ]);
            }
        }

        return $score;
    }
}
