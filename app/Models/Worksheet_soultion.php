<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Worksheet_soultion extends Model
{
    /** @use HasFactory<\Database\Factories\WorksheetSoultionFactory> */
    use HasFactory;

    protected $fillable = ['worksheet_id', 'student_id','score'];
}
