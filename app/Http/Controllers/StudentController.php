<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentBulkUpload;
use App\Http\Requests\StudentUpload;
use App\Imports\StudentsImport;
use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(StudentUpload $request)
    {
        //
        $classroom = Classroom::findOrFail($request->classroom_id);

        $this->authorize('update', $classroom);

        Student::create([
            'name' => $request->name,
            'classroom_id' => $request->classroom_id,
        ]);

        return response()->json([
            'message' => 'Sikeres diák létrehozás.',
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
        $request->validate([
            'name' => 'required|string|max:30',
        ], [

            'name.required' => 'Hiányzó adatok.',
            'name.string' => 'Nem megfelelő adat.',
            'name.max' => 'Nem megfelelő adat.',
        ]);
        $student = Student::with('classroom')->findOrFail($id);

        $this->authorize('update', $student->classroom);

        $student->update(['name' => $request->name]);

        return response()->json([
            'message' => 'Sikeres változtatások.',
        ]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id,classroom_id,'.$request->classroom_id,
        ], [
            'classroom_id.exists' => 'A megadott osztály nem létezik az adatbázisban.',
            'student_ids.required' => 'Legalább egy diákot ki kell választanod a törléshez.',
            'student_ids.*.exists' => 'A kijelölt diákok közül egy vagy több nem ehhez az osztályhoz tartozik!',
        ]);
        $classroom = Classroom::findOrFail($request->classroom_id);
        $this->authorize('delete', $classroom);

        Student::where('classroom_id', $request->classroom_id)->whereIn('id', $request->student_ids)->delete();

        return response()->json([
            'message' => 'Sikeres törlés!',
        ]);
    }

    public function bulkUpload(StudentBulkUpload $request)
    {
        $classroom = Classroom::findOrFail($request->classroom_id);

        $this->authorize('update', $classroom);

        Excel::import(new StudentsImport($request->classroom_id), $request->file('file'));

        return response()->json(['message' => 'A névsor importálása sikeresen megtörtént!']);
    }
}
