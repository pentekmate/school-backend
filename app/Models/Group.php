<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    //
    public function task_grouping()
    {
        return $this->belongsTo(Task_grouping::class, 'task_grouping_id');
    }

    public function group_items()
    {
        return $this->hasMany(Group_Item::class, 'group_id');
    }
}
