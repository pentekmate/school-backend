<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pair_question extends Model
{
    /** @use HasFactory<\Database\Factories\PairQuestionFactory> */
    use HasFactory;

    protected $fillable = ['question', 'imgURL'];

    public function pair()
    {
        return $this->belongsTo(Pair_groups::class, 'pair_group_id');
    }
}
