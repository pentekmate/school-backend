<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task_assignment extends Model
{
    //
    use HasFactory;

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function image()
    {
        return $this->hasOne(Task_assignment_image::class, 'task_assignment_id');
    }
}
