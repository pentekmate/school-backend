<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorksheetRequest;
use App\Http\Resources\WorksheetResource;
use App\Http\Resources\WorksheetResultResource;
use App\Models\Worksheet;
use App\Services\Tasks\StoreAssignmentService;
use App\Services\Tasks\StoreGroupingTaskService;
use App\Services\Tasks\StorePairingTaskService;
use App\Services\Tasks\StoreShortAnswerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WorkSheetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $worksheets = Worksheet::with([
            'tasks.task_type',
            'tasks.task_grouping.groups.items',
            'tasks.task_pair',
            'tasks.task_shortAnswer.questions.answer',
            'tasks.task_assignment.image.assignmentCoordinates.assignmentAnswers',
        ])->get();

        return WorksheetResource::collection($worksheets);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        StoreWorksheetRequest $request,
        StoreGroupingTaskService $groupingService,
        StorePairingTaskService $pairingService,
        StoreShortAnswerService $shortAnswerService,
        StoreAssignmentService $assignmentService)
    {
        DB::transaction(function () use ($request, $groupingService, $pairingService, $shortAnswerService, $assignmentService) {

            $worksheet = Worksheet::create([
                'title' => $request->title,
                'user_id' => $request->user_id,
                'subject_id' => $request->subject_id,
                'lifetime_minutes' => 60,
                'max_time_to_resolve_minutes' => 45,
                'max_points' => $request->max_points,
                'is_public' => $request->is_public,
            ]);

            if ($request->has('assignments') && is_array($request->assignments)) {

                $classroomsData = [];
                foreach ($request->assignments as $assignment) {
                    $classroomsData[$assignment['classroom_id']] = [
                        'access_code' => Str::random(8),
                        'password' => $assignment['password'],
                    ];
                }

                $worksheet->classrooms()->sync($classroomsData);
            }

            foreach ($request->tasks as $taskData) {

                $task = $worksheet->tasks()->create([
                    'task_title' => $taskData['task_title'],
                    'task_description' => $taskData['task_description'],
                    'task_type_id' => $taskData['task_type_id'],

                ]);

                if ($taskData['task_type_id'] == 1) {
                    $groupingService->store($task, $taskData);
                }

                if ($taskData['task_type_id'] == 2) {
                    $pairingService->store($task, $taskData);
                }

                if ($taskData['task_type_id'] == 3) {
                    $shortAnswerService->store($task, $taskData);
                }
                if ($taskData['task_type_id'] == 4) {
                    $assignmentService->store($task, $taskData);
                }
            }
        });

        return response()->json(['success' => true]);
    }

    public function userAnswer($worksheetId, $studentId)
    {
        $worksheet = Worksheet::with('tasks')->findOrFail($worksheetId);
        $solution = DB::table('worksheet_solutions')
            ->where('worksheet_id', $worksheetId)
            ->where('student_id', $studentId)
            ->first();

        request()->merge(['current_solution_id' => $solution->id ?? null]);

        return new WorksheetResultResource($worksheet);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $token = $request->header('X-Worksheet-Token');
        $session = Cache::get('active_session_'.$token);

        if (! $session || $session['worksheet_id'] != $id) {
            return response()->json(['message' => 'Nincs jogosultságod a feladatlaphoz!'], 403);
        }

        $worksheet = Worksheet::with([
            'tasks.task_type',
            'tasks.task_grouping.groups.items',
            'tasks.task_pair',
            'tasks.task_shortAnswer.questions.answer',
            'tasks.task_assignment.image.assignmentCoordinates.assignmentAnswers',
        ])->findOrFail($id);

        return new WorksheetResource($worksheet);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
