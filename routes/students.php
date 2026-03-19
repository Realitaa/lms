<?php

use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:user'])->group(function () {
  Route::get('/home', [StudentController::class, 'index'])->name('student.index');
});
