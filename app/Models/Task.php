<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    //
    use HasFactory;

    public function task_type()
    {
        return $this->belongsTo(Task_type::class, 'task_type_id');
    }

    public function worksheet()
    {
        return $this->belongsTo(Worksheet::class, 'worksheet_id');
    }

    public function task_grouping()
    {
        return $this->hasOne(Task_grouping::class, 'task_id');
    }

    public function task_pair()
    {
        return $this->hasOne(Task_Pair::class, 'task_id');
    }

    public function task_shortAnswer()
    {
        return $this->hasOne(Task_shortAnswer::class, 'task_id');
    }

    public function task_assignment()
    {
        return $this->hasOne(Task_assignment::class, 'task_id');
    }
}
