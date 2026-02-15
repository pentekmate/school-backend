<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    //
    public function classroom()
    {
        return $this->belongsTo(Classroom::class, 'classroom_id');
    }
}
