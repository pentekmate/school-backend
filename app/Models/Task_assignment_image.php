<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task_assignment_image extends Model
{
    //

    public function task_assignment()
    {
        return $this->belongsTo(Task_assignment::class, 'task_assignment_id');
    }

    public function task_assignment_answer()
    {
        return $this->belongsTo(Task_assignment_answer::class, 'task_assignment_image_id');
    }
}
