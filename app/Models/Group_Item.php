<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group_Item extends Model
{
    //

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }
}
