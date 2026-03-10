<?php

namespace App\Services\TaskEvaluation;

use App\Services\TaskEvaluation\Evaluators\ShortAnswerEvaluator;
use Exception;

class TaskEvaluatorFactory
{
    public static function make(string $taskType)
    {
        return match ($taskType) {
            'short_answer' => new ShortAnswerEvaluator,
            'grouping' => new Evaluators\GroupingEvaluator,
            'pairing' => new Evaluators\PairingEvaluator,
            'assignment'=>new Evaluators\AssignmentEvaluator,
            default => throw new Exception('Unknown task type: '.$taskType),
        };
    }
}
