<?php

use App\Http\Controllers\LearningController;
use Illuminate\Support\Facades\Route;

Route::get('/learning', [LearningController::class, 'index'])->name('learning.index');
Route::get('/learning/{course:slug}/lessons/{lesson:slug}', [LearningController::class, 'show'])->name('learning.show');