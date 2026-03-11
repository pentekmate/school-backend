<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment_user_answer extends Model
{
    //

    protected $fillable = ['worksheet_solution_id', 'task_assignment_image_id', 'task_assignment_coordinate_id', 'task_assignment_answer_id'];

    public function solution()
    {
        return $this->belongsTo(Worksheet_solution::class, 'worksheet_solution_id');
    }

    public function assignmentPicture()
    {
        return $this->belongsTo(Task_assignment_image::class, 'task_assignment_image_id');
    }

    public function assignmentCoordinate()
    {
        return $this->belongsTo(Task_assignment_coordinate::class, 'task_asignment_coordinate_id');
    }

    public function assigmentAnswer()
    {
        return $this->belongsTo(Task_assignment_answer::class, 'task_assignment_answer_id');
    }
}
