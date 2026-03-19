<?php

namespace App\Policies;

use App\Models\Classroom;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ClassroomPolicy
{
    private function isOwner(User $user, Classroom $classroom): bool|Response
    {
        return $user->id == $classroom->user_id ? Response::allow()
        : Response::deny('Nincs jogod ehhez a művelethez!');
    }

    public function viewAny(User $user): bool|Response
    {
        return true; // Bármelyik tanár beléphet a saját felületére
    }

    public function view(User $user, Classroom $classroom): bool|Response
    {
        return $this->isOwner($user, $classroom);
    }

    public function create(User $user): bool|Response
    {
        return true; // Bármelyik bejelentkezett tanár hozhat létre osztályt
    }

    public function update(User $user, Classroom $classroom): bool|Response
    {
        return $this->isOwner($user, $classroom);
    }

    public function delete(User $user, Classroom $classroom): bool|Response
    {
        return $this->isOwner($user, $classroom);
    }
}
