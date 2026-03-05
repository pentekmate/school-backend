<?php

namespace App\Services\TaskEvaluation\Evaluators;

use App\Models\Task_shortAnswer_question;
use App\Models\ShortAnswer_user_answer;
class ShortAnswerEvaluator
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
      
        $question = Task_shortAnswer_question::with('answer')
            ->find($sol['question_id']);

        if (!$question) continue;

        ShortAnswer_user_answer::create([
            'worksheet_solution_id'         => $solutionId,
            'task_short_answer_question_id' => $question->id, 
            'user_answer'              => $sol['answer'], 
        ]);

        $submitted = $this->normalize($sol['answer']);
        $correct   = $this->normalize($question->answer->answer ?? '');

        if ($submitted === $correct) {
            $score++;
        }
    }

    return $score;
}

    /**
     * Szöveg normalizálása (kisbetű, trim)
     */
    private function normalize(string $text): string
    {
        return mb_strtolower(trim($text));
    }
}
