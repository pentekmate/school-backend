<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorksheetRequest;
use App\Http\Resources\WorksheetResource;
use App\Http\Resources\WorksheetResultResource;
use App\Models\Student;
use App\Models\Worksheet;
use App\Services\Tasks\StoreAssignmentService;
use App\Services\Tasks\StoreGroupingTaskService;
use App\Services\Tasks\StorePairingTaskService;
use App\Services\Tasks\StoreShortAnswerService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WorkSheetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $user = $request->user();

        $worksheets = Worksheet::where('user_id', $user->id)->get(['title', 'id']);

        return response()->json($worksheets);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWorksheetRequest $request, StoreGroupingTaskService $grouping, StorePairingTaskService $pairing, StoreShortAnswerService $short, StoreAssignmentService $assign)
    {
        return $this->saveWorksheet(new Worksheet, $request, $grouping, $pairing, $short, $assign);
    }

    public function userAnswer($worksheetId, $studentId)
    {

        $worksheet = Worksheet::with('tasks')->findOrFail($worksheetId);
        $student = Student::findOrFail($studentId);

        if ($student->classroom->user_id != Auth::id()) {
            return response()->json([
                'message' => 'Nincs jogod ehhez a művelethez!',
            ], 403);
        }

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

    // diakoldalon a show funkcio ami elkuldi a feladatlapot az adott diáknak
    public function show(Request $request, string $id)
    {
        $token = $request->header('X-Worksheet-Token');
        // $session = Cache::get('active_session_'.$token);

        // if (! $session || $session['worksheet_id'] != $id) {
        //     return response()->json(['message' => 'Nincs jogosultságod a feladatlaphoz!'], 403);
        // }

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
    public function update(StoreWorksheetRequest $request, string $id, StoreGroupingTaskService $grouping, StorePairingTaskService $pairing, StoreShortAnswerService $short, StoreAssignmentService $assign)
    {
        $worksheet = Worksheet::findOrFail($id);
        $this->authorize('update', $worksheet);

        return $this->saveWorksheet($worksheet, $request, $grouping, $pairing, $short, $assign);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $worksheet = Worksheet::findOrFail($id);
        $userId = Auth::id();

        if ($worksheet->user_id != $userId) {
            return response()->json([
                'message' => 'Nincs jogod ehhez a művelethez.',
            ], 403);
        }

        $worksheet->delete();

        return response()->json([
            'message' => 'Sikeres törlés.',
        ]);
    }

    private function saveWorksheet(Worksheet $worksheet, $request, $grouping, $pairing, $short, $assign)
    {
        DB::transaction(function () use ($request, $worksheet, $grouping, $pairing, $short, $assign) {

            // Alapadatok mentése
            $worksheet->fill([
                'title' => $request->title,
                'user_id' => Auth::id(),
                'subject_id' => $request->subject_id,
                'max_points' => $request->max_points,
                'is_public' => $request->is_public,
                'lifetime_minutes' => $request->lifetime_minutes,
                'max_time_to_resolve_minutes' => $request->max_time_to_resolve_minutes,
            ])->save();

            // Osztályok szinkronizálása
            if ($request->has('assignments')) {
                $syncData = [];
                foreach ($request->assignments as $a) {
                    $syncData[$a['classroom_id']] = [
                        'access_code' => Str::random(8),
                        'password' => $a['password'],
                    ];
                }
                $worksheet->classrooms()->sync($syncData);
            }

            // Taskok frissítése: Törlés és Újraírás
            if ($worksheet->wasRecentlyCreated === false) {
                $worksheet->tasks()->delete();
            }

            foreach ($request->tasks as $taskData) {
                $task = $worksheet->tasks()->create([
                    'task_title' => $taskData['task_title'],
                    'task_description' => $taskData['task_description'],
                    'task_type_id' => $taskData['task_type_id'],
                ]);

                match ((int) $taskData['task_type_id']) {
                    1 => $grouping->store($task, $taskData),
                    2 => $pairing->store($task, $taskData),
                    3 => $short->store($task, $taskData),
                    4 => $assign->store($task, $taskData),
                };
            }
        });

        return response()->json(['success' => true]);
    }
}
