<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Worksheet;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\DB;

class WorksheetPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function update(User $user, Worksheet $worksheet): Response
    {

        if ($user->id !== $worksheet->user_id) {
            return Response::deny('Ez nem a te feladatlapod.');
        }

        $hasSolutions = DB::table('worksheet_solutions')
            ->where('worksheet_id', $worksheet->id)
            ->exists();

        if ($hasSolutions) {
            return Response::deny('A feladatlap már nem módosítható, mert diákok elkezdték kitölteni.');
        }

        return Response::allow();
    }

    public function show(User $user, Worksheet $worksheet): Response
    {

        if ($user->id !== $worksheet->user_id) {
            return Response::deny('Ez nem a te feladatlapod.');
        }

        return Response::allow();
    }
}
