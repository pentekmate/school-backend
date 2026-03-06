<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShortAnswer_user_answer extends Model
{
    protected $fillable = ['task_short_answer_question_id', 'user_answer', 'worksheet_solution_id'];

    public function solution()
    {
        return $this->belongsTo(Worksheet_solution::class, 'worksheet_solution_id');
    }

    public function question()
    {
        return $this->belongsTo(Task_shortAnswer_question::class, 'task_short_answer_question_id');
    }
}
