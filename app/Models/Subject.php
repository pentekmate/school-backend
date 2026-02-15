<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    //

    public function worksheets()
    {
        return $this->hasMany(Worksheet::class, 'worksheet_id');
    }

    public function task_types()
    {
        return $this->hasMany(Task_type::class, 'task_type_id');
    }
}
