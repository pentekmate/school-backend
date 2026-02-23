<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pair extends Model
{
    //
    use HasFactory;

    public function task_pair()
    {
        return $this->belongsTo(Task_pair::class, 'task_pair_id');
    }
}
