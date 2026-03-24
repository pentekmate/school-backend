<?php

namespace App\Http\Controllers;

use App\Models\Worksheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class WorksheetAccesController extends Controller
{
    // megnezi hogy egy diak hozzaferhet -e egy adott feladatlahoz, ha igen megkapja a diakok listajat
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

        $alreadyFinished = DB::table('worksheet_solutions')
            ->where('student_id', $request->student_id)
            ->where('worksheet_id', $request->worksheet_id)
            ->exists();

        if ($alreadyFinished) {
            return response()->json(['message' => 'Már egyszer kitöltötted!'], 403);
        }

        $sessionKey = "active_solver_{$request->student_id}_{$request->worksheet_id}";

        if (Cache::has($sessionKey)) {
            return response()->json([
                'success' => true,
                'token' => Cache::get($sessionKey),
                'message' => 'Folyamatban lévő munkamenet folytatása.',
            ]);
        }

        $tempToken = bin2hex(random_bytes(16));

        $worksheet = Worksheet::find($request->worksheet_id);
        $expireAt = now()->addMinutes($worksheet->max_time_to_resolve_minutes ?? 60);

        Cache::put($sessionKey, $tempToken, $expireAt);
        Cache::put('active_session_'.$tempToken, [
            'student_id' => $request->student_id,
            'worksheet_id' => $request->worksheet_id,
        ], $expireAt);

        return response()->json([
            'success' => true,
            'token' => $tempToken,
        ]);
    }
}

// DIÁK HOZZÁFÉRHETŐSÉGE A FELADATLAP KITOLTESHEZ
