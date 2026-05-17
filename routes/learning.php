<?php

use App\Http\Controllers\LearningController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:user'])->group(function () {
  Route::get('/learning', [LearningController::class, 'index'])->name('learning.index');
  Route::get('/learning/{course:slug}/lessons/{lesson:slug}', [LearningController::class, 'show'])->name('learning.show');
  Route::get('/learning/{course:slug}/quiz/{quiz}', [LearningController::class, 'showQuiz'])->name('learning.quiz');
  Route::post('/learning/quiz/{quiz}/start', [LearningController::class, 'startQuiz'])->name('learning.quiz.start');
  Route::post('/learning/quiz/{quiz}/submit', [LearningController::class, 'submitQuiz'])->name('learning.quiz.submit');
});