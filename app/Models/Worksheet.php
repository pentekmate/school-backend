<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Worksheet extends Model
{
    //
    protected $fillable = ['title', 'user_id', 'classroom_id', 'subject_id', 'lifetime_minutes', 'max_time_to_resolve_minutes', 'grade', 'is_public'];

    use HasFactory;

    public function tasks()
    {
        return $this->hasMany(Task::class, 'worksheet_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
}
