<?php

use App\Http\Controllers\UploadController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    // Upload routes
    Route::post('/uploads/image', [UploadController::class, 'upload'])->name('uploads.image');
    Route::post('/uploads/confirm', [UploadController::class, 'confirmUploads'])->name('uploads.confirm');
});

require __DIR__ . '/users.php';
require __DIR__ . '/courses.php';
require __DIR__ . '/modules.php';
require __DIR__ . '/discussions.php';
require __DIR__ . '/settings.php';
