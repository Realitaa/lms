<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\QuizController;
use Illuminate\Support\Facades\Route;

Route::resource('courses', CourseController::class);

Route::group(['prefix' => 'courses', 'middleware' => ['auth', 'role:admin,editor']], function () {
  // Module routes
  Route::get('/{course}/modules', [ModuleController::class, 'index'])->name('courses.modules.index');
  Route::post('/{course}/modules', [ModuleController::class, 'store'])->name('courses.modules.store');
  Route::put('/{course}/modules/{module}', [ModuleController::class, 'update'])->name('courses.modules.update');
  Route::delete('/{course}/modules/{module}', [ModuleController::class, 'destroy'])->name('courses.modules.destroy');
  Route::post('/{course}/modules/reorder', [ModuleController::class, 'reorder'])->name('courses.modules.reorder');

  // Lesson routes
  Route::post('/modules/{module}/lessons', [LessonController::class, 'store'])->name('modules.lessons.store');
  Route::put('/lessons/{lesson}', [LessonController::class, 'update'])->name('lessons.update');
  Route::delete('/lessons/{lesson}', [LessonController::class, 'destroy'])->name('lessons.destroy');
  Route::post('/{course}/lessons/reorder', [LessonController::class, 'reorder'])->name('courses.lessons.reorder');

  // Quiz routes
  Route::post('/modules/{module}/quizzes', [QuizController::class, 'store'])->name('modules.quizzes.store');
  Route::put('/quizzes/{quiz}', [QuizController::class, 'update'])->name('quizzes.update');
  Route::delete('/quizzes/{quiz}', [QuizController::class, 'destroy'])->name('quizzes.destroy');

  // Question routes
  Route::post('/quizzes/{quiz}/questions', [QuizController::class, 'storeQuestion'])->name('quizzes.questions.store');
  Route::put('/questions/{question}', [QuizController::class, 'updateQuestion'])->name('questions.update');
  Route::delete('/questions/{question}', [QuizController::class, 'destroyQuestion'])->name('questions.destroy');

  // Option routes
  Route::post('/questions/{question}/options', [QuizController::class, 'storeOption'])->name('questions.options.store');
  Route::put('/options/{option}', [QuizController::class, 'updateOption'])->name('options.update');
  Route::delete('/options/{option}', [QuizController::class, 'destroyOption'])->name('options.destroy');
});