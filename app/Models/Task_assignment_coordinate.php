<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task_assignment_coordinate extends Model
{
    //

    public function task_assignment_image()
    {
        return $this->belongsTo(Task_assignment_image::class, 'task_assignment_image_id');
    }

    public function task_assignment_answer()
    {
        return $this->belongsTo(Task_assignment_answer::class, 'task_assignment_coordinate_id');
    }
}
