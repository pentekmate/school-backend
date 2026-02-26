<?php

use App\Http\Controllers\WorksheetController;
use Illuminate\Support\Facades\Route;

Route::apiResource('worksheets', WorksheetController::class)
    ->only(['index', 'store']);
