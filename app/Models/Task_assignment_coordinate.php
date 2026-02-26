<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task_assignment_coordinate extends Model
{
    //
    use HasFactory;

    protected $fillable = ['coordinate'];

    public function assignmentImage()
    {
        return $this->belongsTo(Task_assignment_image::class, 'task_assignment_image_id');
    }

    public function assignmentAnswers()
    {
        return $this->hasMany(Task_assignment_answer::class, 'task_assignment_coordinate_id');
    }
}
