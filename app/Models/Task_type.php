<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task_type extends Model
{
    //
    public function tasks(){
        return $this->hasMany(Task::class,'task_id');
    }

    public function subject(){
        return $this->belongsTo(Subject::class,'subject_id');
    }
}
