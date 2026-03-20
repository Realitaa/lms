<?php

use App\Http\Controllers\QuizController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
  // /learning/{course:slug}/quiz
  Route::get('/learning/quiz', [QuizController::class, 'index'])->name('quiz.index');
});
