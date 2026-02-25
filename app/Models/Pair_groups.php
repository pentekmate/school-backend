<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pair_groups extends Model
{
    /** @use HasFactory<\Database\Factories\PairGroupsFactory> */
    use HasFactory;

    public function task_pair()
    {
        return $this->belongsTo(Task_pair::class, 'task_pair_id');
    }

    public function pairs()
    {
        return $this->hasMany(Pair::class, 'pair_group_id');
    }
}
