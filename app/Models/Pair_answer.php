<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pair_answer extends Model
{
    /** @use HasFactory<\Database\Factories\PairAnswerFactory> */
    use HasFactory;

    protected $fillable = ['answer', 'imgURL'];

    public function pair()
    {
        return $this->belongsTo(Pair_groups::class, 'pair_group_id');
    }

    public function userAnswer()
    {
        return $this->hasMany(Pairing_user_answer::class, 'pair_answer_id');
    }

 
}
