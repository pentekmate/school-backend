<?php

namespace App\Http\Controllers;
use App\Models\Worksheet_solution;
use Illuminate\Http\Request;

class TaskSubmitController extends Controller
{
    public function submit(Request $request)
    {

        $worksheetSolution = Worksheet_solution::create([
            'worksheet_id' => $request['worksheet_id'],
            'student_id' => $request['studentID'],
            'score' => 0, 
        ]);

        $totalScore = 0;
        $taskResults = [];

      
        foreach ($request['tasks'] as $submittedTask) {

     
            $typeMap = [1 => 'grouping', 2 => 'shortAnswer'];
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
