<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentBulkUpload;
use App\Http\Requests\StudentUpload;
use App\Imports\StudentsImport;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($classroom_id, $user_id)
    {

        $students = DB::table('students')
            ->join('classrooms', 'students.classroom_id', '=', 'classrooms.id')
            ->select('students.name', 'students.id')
            ->where('classrooms.user_id', $user_id)
            ->where('classrooms.id', $classroom_id)
            ->get();

        return response()->json([
            'students' => $students,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StudentUpload $request)
    {
        //

        $isOwner = DB::table('classrooms')
        ->where('id',$request->classroom_id)
        ->where('user_id',$request->user_id)
        ->exists();

        if(!$isOwner){
            return response()->json([
                'message'=>'Nincs jogosultságod ehhez a művelethet.'
            ],403);
        }

        Student::create([
            'name'=>$request->name,
            'classroom_id'=>$request->classroom_id,
        ]);

        return response()->json([
            'message'=>'Sikeres diák létrehozás.'
        ]);
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
    public function destroy(Request $request)
    {
        //
        $isOwner = DB::table('classrooms')
            ->where('id', $request->classroom_id)
            ->where('user_id', $request->user_id)
            ->exists();

        if (! $isOwner) {
            return response()->json([
                'message' => 'Nincs jogod ehhez a művelethez.',
            ], 403);
        }

        Student::where('classroom_id', $request->classroom_id)->whereIn('id', $request->student_ids)->delete();

        return response()->json([
            'message' => 'Sikeres törlés!',
        ]);
    }

    public function bulkUpload(StudentBulkUpload $request)
    {

        $isOwner = DB::table('classrooms')
            ->where('id', $request->classroom_id)
            ->where('user_id', $request->user_id)
            ->exists();

        if (! $isOwner) {
            return response()->json(['error' => 'Nincs jogod ide tölteni'], 403);
        }

        Excel::import(new StudentsImport($request->classroom_id), $request->file('file'));

        return response()->json(['message' => 'A névsor importálása sikeresen megtörtént!']);
    }
}
