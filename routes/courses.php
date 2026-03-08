<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\LessonController;
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
});