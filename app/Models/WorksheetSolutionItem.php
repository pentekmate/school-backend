<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorksheetSolutionItem extends Model
{
    protected $fillable = ['task_id', 'score', 'worksheet_solution_id'];

    public function solution()
    {
        return $this->belongsTo(WorksheetSolution::class);
    }
}
