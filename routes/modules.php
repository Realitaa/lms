<?php

use App\Http\Controllers\ModuleController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'courses', 'middleware' => ['auth']], function () {
  Route::get('/{course}/modules', [ModuleController::class, 'index'])->name('courses.modules.index')->middleware(['auth', 'role:admin,editor']);
});