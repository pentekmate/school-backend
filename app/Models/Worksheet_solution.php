<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Worksheet_solution extends Model
{
    /** @use HasFactory<\Database\Factories\WorksheetSoultionFactory> */
    use HasFactory;

    protected $fillable = ['worksheet_id', 'student_id', 'score'];

    public function ShortAnswerUserAnswers()
    {
        return $this->hasMany(ShortAnswer_user_answer::class, 'worksheet_solution_id');
    }
}
