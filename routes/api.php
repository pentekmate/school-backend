<?php

use App\Http\Controllers\TaskSubmitController;
use App\Http\Controllers\WorksheetAccesController;
use App\Http\Controllers\WorksheetController;
use Illuminate\Support\Facades\Route;

Route::apiResource('worksheets', WorksheetController::class)
    ->only(['index', 'store', 'show']);

Route::post('/worksheet/submit', [TaskSubmitController::class, 'submit']);
Route::get('/worksheet/{worksheetId}/userAnswer/{studentId}', [WorksheetController::class, 'userAnswer']);
// Route::post('/worksheets/{id}',WorksheetController::class)->only(['show']);

Route::post('/verify-access', [WorksheetAccesController::class, 'verifyAcces']);
Route::post('/start-solving', [WorksheetAccesController::class, 'startSolving']);
