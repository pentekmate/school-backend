<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmitWorksheetRequest;
use App\Models\Worksheet_solution;

class TaskSubmitController extends Controller
{
    public function submit(SubmitWorksheetRequest $request)
    {

        $worksheetSolution = Worksheet_solution::create([
            'worksheet_id' => $request['worksheet_id'],
            'student_id' => $request['student_id'],
            'score' => 0,
        ]);

        $totalScore = 0;
        $taskResults = [];

        foreach ($request['tasks'] as $submittedTask) {

            $typeMap = [1 => 'grouping', 2 => 'pairing', 3 => 'short_answer', 4 => 'assignment'];
            $typeName = $typeMap[$submittedTask['task_type_id']];

            $evaluator = \App\Services\TaskEvaluation\TaskEvaluatorFactory::make($typeName);

            $taskScore = $evaluator->evaluate(
                $submittedTask['task_id'],
                $submittedTask['solutions'],
                $worksheetSolution->id
            );

            $totalScore += $taskScore;
            $taskResults[] = [
                'task_id' => $submittedTask['task_id'],
                'score' => $taskScore,
            ];
        }

        $worksheetSolution->update(['score' => $totalScore]);

        return response()->json([
            'message' => 'Sikeres mentés!',
            'total_score' => $totalScore,
            'results' => $taskResults,
        ]);
    }
}
