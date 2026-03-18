<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmitWorksheetRequest;
use App\Models\Worksheet_solution;
use Illuminate\Support\Facades\Cache;

class TaskSubmitController extends Controller
{
    public function submit(SubmitWorksheetRequest $request)
    {

        $token = $request->header('X-Worksheet-Token');
        $session = Cache::get('active_session_'.$token);

        if (! $session) {
            return response()->json(['message' => 'Lejárt munkamenet!'], 403);
        }

        $worksheetSolution = Worksheet_solution::create([
            'worksheet_id' => $session['worksheet_id'],
            'student_id' => $session['student_id'],
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

        Cache::forget('active_session_'.$token);

        return response()->json([
            'message' => 'Sikeres mentés!',
            'total_score' => $totalScore,
            'results' => $taskResults,
        ]);
    }
}
