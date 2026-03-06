<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pairing_user_answer extends Model
{
    //
    protected $fillable = ['worksheet_solution_id','pair_question_id','pair_answer_id'];


    public function solution(){
        return $this->belongsTo(Worksheet_solution::class,'worksheet_solution_id');
    }

    public function question(){
        return $this->belongsTo(Pair_question::class,'pair_question_id');
    }

    public function answer(){
        return $this->belongsTo(Pair_answer::class,'pair_answer_id');
    }
}
