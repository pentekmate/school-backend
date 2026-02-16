<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task_shortAnswer extends Model
{
    //

    public function task_shortAnswer_image()
    {
        return $this->belongsTo(Task_shortAnswer_image::class, 'task_shortAnswer_image_id');
    }
}
