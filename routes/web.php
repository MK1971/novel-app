<?php

use App\Http\Controllers\Admin\ChapterController as AdminChapterController;
use App\Http\Controllers\Admin\EditApprovalController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\EditController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VoteController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/chapters', [ChapterController::class, 'index'])->name('chapters.index');
Route::get('/chapters/{chapter}', [ChapterController::class, 'show'])->name('chapters.show');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/leaderboard', function () {
    $adminEmail = env('ADMIN_EMAIL', 'admin@example.com');
    $users = User::where('email', '!=', $adminEmail)
        ->orderByDesc('points')
        ->limit(20)
        ->get();
    return view('leaderboard', compact('users'));
})->name('leaderboard');

Route::get('/vote', [VoteController::class, 'index'])->name('vote.index');

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    Route::post('/payment/checkout', [PaymentController::class, 'checkout'])->name('payment.checkout');
    Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/chapters/{chapterId}/edit', [EditController::class, 'create'])->name('edits.create');
    Route::post('/edits', [EditController::class, 'store'])->name('edits.store');
    Route::post('/vote/{chapter}', [VoteController::class, 'store'])->name('vote.store');

    Route::middleware('can:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/edits', [EditApprovalController::class, 'index'])->name('edits.index');
        Route::post('/edits/{edit}/approve', [EditApprovalController::class, 'approve'])->name('edits.approve');
        Route::post('/edits/{edit}/reject', [EditApprovalController::class, 'reject'])->name('edits.reject');
        Route::get('/chapters', [AdminChapterController::class, 'index'])->name('chapters.index');
        Route::post('/chapters/story', [AdminChapterController::class, 'storeStoryChapter'])->name('chapters.store-story');
        Route::post('/chapters/peter-trull', [AdminChapterController::class, 'storePeterTrullChapter'])->name('chapters.store-peter-trull');
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    });
});

require __DIR__.'/auth.php';
