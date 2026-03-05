<?php

use App\Http\Controllers\TaskSubmitController;
use App\Http\Controllers\WorksheetController;
use Illuminate\Support\Facades\Route;

Route::apiResource('worksheets', WorksheetController::class)
    ->only(['index', 'store']);

Route::post('/tasks/submit', [TaskSubmitController::class, 'submit']);
