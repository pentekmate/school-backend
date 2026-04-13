<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassroomController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user_id = Auth::id();
        $classrooms = Classroom::where('user_id', $user_id)->get(['name', 'id']);

        return response()->json($classrooms);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:30',
        ], [

            'name.required' => 'Hiányzó adatok.',
            'name.string' => 'Nem megfelelő adat.',
            'name.max' => 'Nem megfelelő adat.',
        ]);

        Classroom::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
        ]);

        return response()->json([
            'message' => 'Sikeres osztály létrehozás.',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $classroom_id)
    {
        $classroom = Classroom::findOrFail($classroom_id);
        $this->authorize('view', $classroom);

        return response()->json([
            'students' => $classroom->students,
            'classroom_name' => $classroom->name,
            'classroom_id' => $classroom_id,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $classroom_id)
    {
        $request->validate([
            'name' => 'required|string|max:30',
        ], [

            'name.required' => 'Hiányzó adatok.',
            'name.string' => 'Nem megfelelő adat.',
            'name.max' => 'Nem megfelelő adat.',
        ]);
        $classroom = Classroom::findOrFail($classroom_id);
        $this->authorize('update', $classroom);

        $classroom->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'message' => 'Sikeres módosítás.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $classroom_id)
    {
        $classroom = Classroom::findOrFail($classroom_id);
        $this->authorize('delete', $classroom);

        $classroom->delete();

        return response()->json([
            'message' => 'Sikeres osztály törlés.',
        ]);
    }
}
