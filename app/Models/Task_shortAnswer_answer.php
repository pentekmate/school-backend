<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task_shortAnswer_answer extends Model
{
    //

    public function task_shortAnswer_images()
    {
        return $this->hasMany(Task_shortAnswer_image::class, 'task_shortAnswer_id');
    }
}
