<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    //

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
        return $this->belongsTo(Task_grouping::class, 'task_id');
    }

    public function task_pair()
    {
        return $this->belongsTo(Task_Pair::class, 'task_id');
    }

    public function task_shortAnswer()
    {
        return $this->belongsTo(Task_shortAnswer::class, 'task_id');
    }

    public function task_assignment()
    {
        return $this->belongsTo(Task_assignment::class, 'task_id');
    }
}
