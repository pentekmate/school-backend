<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task_assignment_image extends Model
{
    //
    use HasFactory;

    public function task_assignment()
    {
        return $this->belongsTo(Task_assignment::class, 'task_assignment_id');
    }

    public function assignmentCoordinates()
    {
        return $this->hasMany(task_assignment_coordinate::class, 'task_assignment_image_id');
    }
}
