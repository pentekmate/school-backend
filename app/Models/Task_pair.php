<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task_pair extends Model
{
    //
    use HasFactory;

    protected $fillable = ['feedback'];

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function pairGroups()
    {
        return $this->hasMany(Pair_groups::class, 'task_pair_id');
    }
}
