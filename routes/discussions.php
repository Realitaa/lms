<?php

use App\Http\Controllers\DiscussionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/discussions', [DiscussionController::class, 'index'])->name('discussions.index');
    Route::post('/discussions', [DiscussionController::class, 'store'])->name('discussions.store');
    Route::post('/discussions/{thread}/replies', [DiscussionController::class, 'storeReply'])->name('discussions.replies.store');
    Route::delete('/discussions/{thread}', [DiscussionController::class, 'destroy'])->name('discussions.destroy');
    Route::delete('/discussions/replies/{reply}', [DiscussionController::class, 'destroyReply'])->name('discussions.replies.destroy');
});
