<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    //

    public function task_type(){
        return $this->belongsTo(Task_type::class,'task_type_id');
    }
    public function worksheet(){
        return $this->belongsTo(Worksheet::class,'worksheet_id');
    }
}
