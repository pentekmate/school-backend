<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task_assignment_answer extends Model
{
    use HasFactory;

    public function coordinate()
    {
        return $this->belongsTo(Task_assignment_coordinate::class, 'task_assignment_coordinate_id');
    }
}
