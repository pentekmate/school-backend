<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task_shortAnswer_image extends Model
{
    //

    public function task_shortAnswer()
    {
        return $this->belongsTo(Task_shortAnswer::class, 'task_shortAnswer_id');
    }

    public function task_shortAnswer_answer()
    {
        return $this->belongsTo(Task_shortAnswer_answer::class, 'task_shortAnswer_image_id');
    }
}
