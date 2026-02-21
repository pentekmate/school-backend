<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    //
    use HasFactory;

    public function task_grouping()
    {
        return $this->belongsTo(Task_grouping::class, 'task_grouping_id');
    }

    public function items()
    {
        return $this->hasMany(GroupItem::class, 'group_id');
    }
}
