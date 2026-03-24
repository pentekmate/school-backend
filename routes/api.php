<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TaskSubmitController;
use App\Http\Controllers\WorksheetAccesController;
use App\Http\Controllers\WorksheetController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

// Route::post('/worksheets/{id}',WorksheetController::class)->only(['show']);

Route::post('/verify-access', [WorksheetAccesController::class, 'verifyAcces']);
Route::post('/start-solving', [WorksheetAccesController::class, 'startSolving']);
Route::post('/worksheet/submit', [TaskSubmitController::class, 'submit']);

Route::middleware('auth:sanctum')->group(function () {
    // classrooms
    Route::apiResource('/classrooms', ClassroomController::class);
    // students
    Route::post('/students/delete', [StudentController::class, 'destroy']);
    Route::post('/students/bulk-upload', [StudentController::class, 'bulkUpload']);
    Route::post('/students/upload', [StudentController::class, 'store']);
    Route::post('/students/update/{student_id}', [StudentController::class, 'update']);
    // worksheets
    Route::apiResource('worksheets', WorksheetController::class)
        ->only(['index', 'store', 'destroy', 'update']);
    Route::get('/worksheet/{worksheetId}/userAnswer/{student_id}', [WorksheetController::class, 'userAnswer']);
    Route::post('media-upload', [MediaController::class, 'upload']);

    Route::get('/logout', [AuthController::class, 'logout']);
});
Route::apiResource('worksheets', WorksheetController::class)
    ->only(['show']);
// auth
Route::post('/login', [AuthController::class, 'login']);


Route::get('/debug-file', function () {
    $path = 'temp/9d0dd343-c550-4d80-a4d2-bd07bb53b18e.png';
    return [
        'exists_on_disk' => Storage::disk('public')->exists($path),
        'full_path' => storage_path('app/public/' . $path),
        'is_readable' => is_readable(storage_path('app/public/' . $path)),
    ];
});