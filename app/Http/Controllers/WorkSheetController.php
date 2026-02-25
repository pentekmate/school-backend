<?php

namespace App\Http\Controllers;

use App\Http\Resources\WorksheetResource;
use App\Models\Worksheet;
use Illuminate\Http\Request;

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
        //
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
