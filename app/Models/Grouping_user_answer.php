<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grouping_user_answer extends Model
{
    //

    protected $fillable = ['worksheet_solution_id', 'group_id', 'group_item_id'];

    public function solution()
    {
        return $this->belongsTo(Worksheet_solution::class, 'worksheet_solution_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function groupItem()
    {
        return $this->belongsTo(GroupItem::class, 'group_item_id');
    }
}
