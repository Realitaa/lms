<?php

use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:user'])->group(function () {
  Route::get('/home', [StudentController::class, 'index'])->name('student.index');
  Route::get('/discover', [StudentController::class, 'discover'])->name('student.discover');
  Route::post('/courses/{course}/enroll', [StudentController::class, 'enroll'])->name('student.courses.enroll');
});
