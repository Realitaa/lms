<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'users', 'middleware' => ['auth', 'role:admin']], function () {
  Route::get('/', [UserController::class, 'index'])->name('users.index');
  Route::post('/', [UserController::class, 'store'])->name('users.store');
  Route::put('/{id}', [UserController::class, 'update'])->name('users.update');
  Route::delete('/{id}', [UserController::class, 'destroy'])->name('users.destroy');
});
