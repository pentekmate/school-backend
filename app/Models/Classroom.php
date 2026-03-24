<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    //
    protected $fillable = ['user_id', 'name'];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'classroom_id');
    }

    public function worksheets()
    {
        return $this->belongsToMany(Worksheet::class, 'class_worksheet');
    }
}
