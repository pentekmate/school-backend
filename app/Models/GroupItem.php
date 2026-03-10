<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupItem extends Model
{
    //
    use HasFactory;

    protected $fillable = ['name', 'imgUrl'];

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function userAnswer()
    {
        return $this->hasMany(Grouping_user_answer::class, 'group_id');
    }
}
