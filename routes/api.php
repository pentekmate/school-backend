<?php

use App\Http\Controllers\WorksheetController;
use Illuminate\Support\Facades\Route;

Route::get('/worksheets', [WorksheetController::class, 'index']);
