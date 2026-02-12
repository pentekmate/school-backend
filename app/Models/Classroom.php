<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    //
    public function teacher(){
        return $this->belongsTo(User::class,'user_id');
    }
}
