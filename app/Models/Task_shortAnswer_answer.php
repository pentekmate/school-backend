<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task_shortAnswer_answer extends Model
{
    //
    use HasFactory;

    protected $fillable = ['answer', 'imgURL'];

    public function question()
    {
        return $this->belongsTo(Task_shortAnswer_question::class, 'task_short_answer_question_id');
    }
}
