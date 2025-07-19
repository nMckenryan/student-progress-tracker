<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\StudentController;

Route::get('/', [StudentController::class, 'index']);
Route::post('/update-progress', [StudentController::class, 'updateProgress'])->name('update.progress');