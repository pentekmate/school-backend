<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TaskEvaluation\TaskEvaluatorFactory;

class TaskSubmitController extends Controller
{
    public function submit(Request $request)
    {
        $worksheetId = $request->worksheet_id;
        $studentId = $request->studentID;
        $submittedTasks = $request->tasks;

        $totalScore = 0;
        $taskResults = [];

        foreach ($submittedTasks as $submittedTask) {

            $taskId = $submittedTask['task_id'];
            $taskType = $submittedTask['task_type']; // vagy task_type_id map a típusra

            // Task evaluator kiválasztása
            $evaluator = TaskEvaluatorFactory::make($taskType);

            // Kiértékelés
            $taskScore = $evaluator->evaluate($taskId, $submittedTask['solutions']);

            $totalScore += $taskScore;

            $taskResults[] = [
                'task_id' => $taskId,
                'score'   => $taskScore
            ];

            // 💡 Itt mentheted az egyes task attempteket az adatbázisba
            // TaskAttempt::create([...]);
        }

        // WorksheetAttempt tárolása
        // WorksheetAttempt::create([
        //     'worksheet_id' => $worksheetId,
        //     'student_id' => $studentId,
        //     'score' => $totalScore,
        // ]);

        return response()->json([
            'tasks' => $taskResults,
            'total_score' => $totalScore
        ]);
    }
}