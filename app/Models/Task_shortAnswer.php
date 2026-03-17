<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task_shortAnswer extends Model
{
    //
    use HasFactory;

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function questions()
    {
        return $this->hasMany(Task_shortAnswer_question::class, 'task_short_answers_id');
    }
}
