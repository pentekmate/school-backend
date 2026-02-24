<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task_shortAnswer_answer extends Model
{
    //
    use HasFactory;

    public function question()
    {
        return $this->belongsTo(Task_shortAnswer_question::class,  'task_short_answer_question_id');
    }
}
