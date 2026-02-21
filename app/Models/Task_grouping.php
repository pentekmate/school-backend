<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task_grouping extends Model
{
    //
    use HasFactory;

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function groups()
    {
        return $this->hasMany(Group::class, 'task_grouping_id');
    }
}
