<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task_shortAnswer_question extends Model
{
    //
    use HasFactory;

    public function shortAnswer()
    {
        return $this->belongsTo(Task_shortAnswer::class, 'task_short_answers_id');
    }

    public function answer()
    {
        return $this->hasOne(Task_shortAnswer_answer::class, 'task_short_answer_question_id');
    }
}
