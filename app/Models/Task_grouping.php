<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task_grouping extends Model
{
    //

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function groups()
    {
        return $this->hasMany(Group::class, 'task_grouping_id');
    }
}
