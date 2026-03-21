<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\EditController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\AchievementController;
use App\Http\Controllers\ActivityFeedController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\EditApprovalController;
use App\Http\Controllers\Admin\ChapterUploadController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\FeedbackManagementController;
use App\Http\Controllers\ModerationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/about', function () {
    return view('about');
})->name('about');

// Public routes
Route::get('/chapters', [ChapterController::class, 'index'])->name('chapters.index');
Route::get('/chapters/{chapter}', [ChapterController::class, 'show'])->name('chapters.show');
Route::post('/chapters/{chapter}/track-progress', [ChapterController::class, 'trackProgress'])->middleware('auth')->name('chapters.track-progress');
Route::get('/chapters/{chapter}/get-progress', [ChapterController::class, 'getProgress'])->middleware('auth')->name('chapters.get-progress');

Route::get('/vote', [VoteController::class, 'index'])->name('vote.index');
Route::post('/vote/{chapter}', [VoteController::class, 'store'])->middleware('auth')->name('vote.store');

Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard');
Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
Route::get('/archive', function () {
    return view('archive.index');
})->name('archive.chapters');

Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback.index');
Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/edits', [EditController::class, 'index'])->name('edits.index');
    Route::post('/edits', [EditController::class, 'store'])->name('edits.store');

    Route::get('/achievements/show', [AchievementController::class, 'show'])->name('achievements.show');
    Route::get('/activity-feed', [ActivityFeedController::class, 'index'])->name('activity-feed.index');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');

    Route::post('/payment/checkout', [PaymentController::class, 'checkout'])->name('payment.checkout');
    Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/edits', [EditApprovalController::class, 'index'])->name('edits.index');
    Route::post('/edits/{edit}/approve', [EditApprovalController::class, 'approve'])->name('edits.approve');
    Route::post('/edits/{edit}/reject', [EditApprovalController::class, 'reject'])->name('edits.reject');

    Route::get('/chapters', [ChapterUploadController::class, 'index'])->name('chapters.index');
    Route::post('/chapters', [ChapterUploadController::class, 'store'])->name('chapters.store');

    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');

    Route::get('/feedback', [FeedbackManagementController::class, 'index'])->name('feedback.index');

    Route::get('/inline-edits', [ModerationController::class, 'inlineEdits'])->name('inline-edits.index');
    Route::post('/inline-edits/{inlineEdit}/approve', [ModerationController::class, 'approveInlineEdit'])->name('inline-edits.approve');
    Route::post('/inline-edits/{inlineEdit}/reject', [ModerationController::class, 'rejectInlineEdit'])->name('inline-edits.reject');
});

// Auth routes
require __DIR__.'/auth.php';
