<?php

namespace App\Services\TaskEvaluation;

use Exception;

class TaskEvaluatorFactory
{
    public static function make(string $taskType)
    {
        return match ($taskType) {
            'shortAnswer' => new Evaluators\ShortAnswerEvaluator(),
            // 'grouping'    => new Evaluators\GroupingEvaluator(),
            // 'pairing'     => new Evaluators\PairingEvaluator(),
            default       => throw new Exception('Unknown task type: '.$taskType),
        };
    }
}