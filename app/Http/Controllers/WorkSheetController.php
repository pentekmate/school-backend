<?php

namespace App\Http\Controllers;

use App\Http\Resources\WorksheetResource;
use App\Models\Worksheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'tasks.task_assignment.image.assignmentCoordinates.assignmentAnswer',
        ])->get();

        return WorksheetResource::collection($worksheets);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::transaction(function () use ($request) {

            $worksheet = Worksheet::create([
                'title' => $request->title,
                'user_id' => 1,
                'subject_id' => 1,
                'classroom_id' => 1,
                'lifetime_minutes' => 60,
                'max_time_to_resolve_minutes' => 45,
                'grade' => 1,
                'is_public' => 0,
            ]);

            foreach ($request->tasks as $taskData) {

                $task = $worksheet->tasks()->create([
                    'task_title' => $taskData['task_title'],
                    'task_description' => $taskData['task_description'],
                    'task_type_id' => $taskData['task_type_id'],

                ]);

                if ($taskData['task_type_id'] == 1) {

                    $grouping = $task->task_grouping()->create([
                        'feedback' => $taskData['feedback'],
                    ]);

                    foreach ($taskData['grouping']['groups'] as $groupData) {

                        $group = $grouping->groups()->create([
                            'name' => $groupData['name'],
                        ]);

                        foreach ($groupData['items'] as $itemData) {
                            $group->items()->create([
                                'name' => $itemData['name'],
                            ]);
                        }
                    }
                }

                if ($taskData['task_type_id'] == 2) {

                    $pairing = $task->task_pair()->create([
                        'feedback' => $taskData['feedback'],
                    ]);

                    foreach ($taskData['pairing']['pairing_groups'] as $groupData) {

                        $pairGroup = $pairing->pairGroups()->create();

                        $pairGroup->questions()->create([
                            'question' => $groupData['pair_question'],
                        ]);

                        $pairGroup->answers()->create([
                            'answer' => $groupData['pair_answer'],
                        ]);
                    }
                }
            }
        });

        return response()->json(['success' => true]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
