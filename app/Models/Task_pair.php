<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task_pair extends Model
{
    //
    use HasFactory;

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function pairs()
    {
        return $this->hasMany(Pair::class, 'task_pair_id');
    }
}
