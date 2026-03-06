<?php

namespace App\Services\TaskEvaluation\Evaluators;

use App\Models\Pair_question;
use App\Models\Pairing_user_answer as ModelsPairing_user_answer;
use Illuminate\Support\Facades\DB;

class PairingEvaluator
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

            $question = Pair_question::find($sol['question_id']);

            if (! $question) {
                continue;
            }

            $isCorrect = DB::table('pair_answers')
                ->where('id', $sol['answer_id'])
                ->where('pair_group_id', $question->pair_group_id)
                ->exists();

            if ($isCorrect) {
                $score++;
            }

            ModelsPairing_user_answer::create([
                'worksheet_solution_id' => $solutionId,
                'pair_question_id' => $sol['question_id'],
                'pair_answer_id' => $sol['answer_id'],
            ]);
        }

        return $score;
    }

}
