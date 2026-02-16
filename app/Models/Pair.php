<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pair extends Model
{
    //

    public function task_pair()
    {
        return $this->belongsTo(Task_pair::class, 'task_pair_id');
    }
}
