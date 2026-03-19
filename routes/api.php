<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TaskSubmitController;
use App\Http\Controllers\WorksheetAccesController;
use App\Http\Controllers\WorksheetController;
use Illuminate\Support\Facades\Route;

Route::post('/worksheet/submit', [TaskSubmitController::class, 'submit']);
// Route::post('/worksheets/{id}',WorksheetController::class)->only(['show']);

Route::post('/verify-access', [WorksheetAccesController::class, 'verifyAcces']);
Route::post('/start-solving', [WorksheetAccesController::class, 'startSolving']);

// students
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/students/{classroom_id}', [StudentController::class, 'index']);
    Route::post('/students/delete', [StudentController::class, 'destroy']);
    Route::post('/students/bulk-upload', [StudentController::class, 'bulkUpload']);
    Route::post('/students/upload', [StudentController::class, 'store']);

    Route::apiResource('worksheets', WorksheetController::class)
        ->only(['index', 'store']);
    Route::get('/worksheet/{worksheetId}/userAnswer/{studentId}', [WorksheetController::class, 'userAnswer']);
});
Route::apiResource('worksheets', WorksheetController::class)
    ->only(['show']);
// auth
Route::post('/login', [AuthController::class, 'login']);
