<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class WorksheetAccesController extends Controller
{
    public function verifyAcces(Request $request)
    {

        $request->validate([
            'access_code' => 'required|string',
            'password' => 'required|string',
        ]);

        $pivot = DB::table('class_worksheets')
            ->where('access_code', $request->access_code)
            ->where('password', $request->password)
            ->first();

        if (! $pivot) {
            return response()->json([
                'message' => 'Érvénytelen kód vagy jelszó.',
            ], 403);
        }

        $students = DB::table('students')
            ->where('classroom_id', $pivot->classroom_id)
            ->get(['name', 'id']);

        return response()->json([
            'success' => true,
            'students' => $students,
            'worksheet_id' => $pivot->worksheet_id,
        ]);
    }

    public function startSolving(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'worksheet_id' => 'required|exists:worksheets,id',
        ]);

        $tempToken = bin2hex(random_bytes(16));

        Cache::put('active_session_'.$tempToken, [
            'student_id' => $request->student_id,
            'worksheet_id' => $request->worksheet_id,
        ], now()->addHours(2));

        return response()->json([
            'success' => true,
            'token' => $tempToken,
        ]);
    }
}
