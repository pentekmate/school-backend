<?php

namespace App\Services\TaskEvaluation\Evaluators;

use App\Models\Grouping_user_answer;
use Illuminate\Support\Facades\DB;

class GroupingEvaluator
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

            $groupId = $sol['group_id'];

            foreach ($sol['group_item_ids'] as $itemId) {
                $isCorrect = DB::table('group_items')
                    ->where('id', $itemId)
                    ->where('group_id', $groupId)
                    ->exists();

                if ($isCorrect) {
                    $score++;
                }

                Grouping_user_answer::create([
                    'worksheet_solution_id' => $solutionId,
                    'group_id' => $groupId,
                    'group_item_id' => $itemId,
                ]);
            }
        }

        return $score;
    }
}
