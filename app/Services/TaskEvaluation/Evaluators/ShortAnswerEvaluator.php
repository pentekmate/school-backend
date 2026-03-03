<?php

namespace App\Services\TaskEvaluation\Evaluators;

use App\Models\Task_shortAnswer_question;

class ShortAnswerEvaluator
{
    /**
     * Short answer típusú task kiértékelése
     *
     * @param int $taskId
     * @param array $solutions
     * @return array
     */
    public function evaluate(int $taskId, array $solutions): int
    {
        $score = 0;

        foreach ($solutions as $solution) {

            $question = Task_shortAnswer_question::where('id', $solution['question_id'])
                ->where('task_id', $taskId)
                ->first();

            if (!$question) {
                continue;
            }

            $submittedAnswer = $this->normalize($solution['answer']);
            $correctAnswer   = $this->normalize($question->correct_answer);

            if ($submittedAnswer === $correctAnswer) {
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