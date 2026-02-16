<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task_assignment extends Model
{
    //

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function task_assignment_image()
    {
        return $this->hasMany(Task_assginment_image::class, 'task_assignment_id');
    }
}
