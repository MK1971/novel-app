<?php

use App\Http\Controllers\AchievementController;
use App\Http\Controllers\ActivityFeedController;
use App\Http\Controllers\Admin\ChapterController as AdminChapterController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\EditController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\InlineEditController;
use App\Http\Controllers\ModerationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ParagraphReactionController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VoteController;
use App\Models\Chapter;
use App\Models\Edit;
use App\Models\InlineEdit;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $acceptedEditStatuses = ['accepted', 'accepted_full', 'accepted_partial'];

    $abbrevInt = static function (int $value): string {
        if ($value < 1000) {
            return (string) $value;
        }
        if ($value < 1_000_000) {
            $k = $value / 1000;
            $rounded = $value >= 10_000 ? (string) (int) round($k) : (string) round($k, 1);

            return rtrim(rtrim($rounded, '0'), '.').'k';
        }

        return rtrim(rtrim((string) round($value / 1_000_000, 1), '0'), '.').'M';
    };

    $contributorsCount = User::query()
        ->where(function ($q) use ($acceptedEditStatuses) {
            $q->whereHas('edits', fn ($e) => $e->whereIn('status', $acceptedEditStatuses))
                ->orWhereHas('inlineEdits', fn ($ie) => $ie->where('status', 'approved'));
        })
        ->count();

    $editsAcceptedCount = Edit::whereIn('status', $acceptedEditStatuses)->count()
        + InlineEdit::where('status', 'approved')->count();

    $chaptersLiveCount = Chapter::where('status', 'published')->count();

    $landingStats = [
        'contributors' => $abbrevInt($contributorsCount),
        'edits_accepted' => $abbrevInt($editsAcceptedCount),
        'chapters_live' => (string) $chaptersLiveCount,
        'prize_pool' => config('marketing.landing_prize_pool_display'),
    ];

    return view('welcome', ['landingStats' => $landingStats]);
})->name('home');

Route::get('/dev/landing-ux-suggestions', function () {
    abort_unless(app()->isLocal(), 404);
    $path = base_path('docs/landing-ux-suggestions.txt');
    $content = is_readable($path) ? file_get_contents($path) : "Missing file: docs/landing-ux-suggestions.txt\n";

    return view('dev.landing-ux-suggestions', ['content' => $content]);
})->name('dev.landing-ux-suggestions');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/chapters', [ChapterController::class, 'index'])->name('chapters.index');
Route::get('/chapters/{chapter}', [ChapterController::class, 'show'])->name('chapters.show');
Route::post('/chapters/{chapter}/track-progress', [ChapterController::class, 'trackProgress'])->middleware('auth')->name('chapters.track-progress');
Route::get('/chapters/{chapter}/get-progress', [ChapterController::class, 'getProgress'])->middleware('auth')->name('chapters.get-progress');

Route::get('/leaderboard', function () {
    $adminEmail = env('ADMIN_EMAIL', 'admin@example.com');
    $users = User::where('email', '!=', $adminEmail)
        ->orderByDesc('points')
        ->limit(20)
        ->get();

    return view('leaderboard', compact('users'));
})->name('leaderboard');

Route::get('/vote', [VoteController::class, 'index'])->name('vote.index');
Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
Route::get('/activity-feed', [ActivityFeedController::class, 'index'])->name('activity-feed.index');
Route::get('/achievements', [AchievementController::class, 'index'])->name('achievements.index');
Route::get('/archive/chapters', [ArchiveController::class, 'chapters'])->name('archive.chapters');
Route::get('/archive/rounds', [ArchiveController::class, 'rounds'])->name('archive.rounds');

Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback.index');
Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $achievements = \App\Models\Achievement::all();
        $userAchievements = auth()->user()->achievements()->pluck('achievement_id')->toArray();

        return view('dashboard', compact('achievements', 'userAchievements'));
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/payment/checkout', [PaymentController::class, 'checkout'])->name('payment.checkout');
    Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');

    Route::get('/chapters/{chapterId}/edit', [EditController::class, 'create'])->name('edits.create');
    Route::post('/edits', [EditController::class, 'store'])->name('edits.store');
    Route::post('/vote/{chapter}', [VoteController::class, 'store'])->name('vote.store');
    Route::post('/chapters/{chapter}/inline-edit', [InlineEditController::class, 'store'])->name('inline-edit.store');
    Route::post('/paragraphs/{chapter}/react', [ParagraphReactionController::class, 'store'])->name('paragraph-reaction.store');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

    Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/edits', [ModerationController::class, 'index'])->name('edits.index');
        Route::post('/edits/{edit}/approve', [ModerationController::class, 'approve'])->name('edits.approve');
        Route::post('/edits/{edit}/reject', [ModerationController::class, 'reject'])->name('edits.reject');

        Route::get('/chapters', [AdminChapterController::class, 'index'])->name('chapters.index');
        Route::post('/chapters/story', [AdminChapterController::class, 'storeStoryChapter'])->name('chapters.store-story');
        Route::post('/chapters/peter-trull', [AdminChapterController::class, 'storePeterTrullChapter'])->name('chapters.store-peter-trull');
        Route::delete('/chapters/{chapter}', [AdminChapterController::class, 'destroy'])->name('chapters.destroy');
        Route::post("/chapters/{chapter}/toggle-lock", [AdminChapterController::class, "toggleLock"])->name("chapters.toggle-lock");
        Route::post("/chapters/{chapter}/archive", [AdminChapterController::class, "archive"])->name("chapters.archive");

        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');

        Route::get('/feedback', [FeedbackController::class, 'adminIndex'])->name('feedback.index');

        Route::get('/inline-edits', [ModerationController::class, 'inlineEdits'])->name('inline-edits.index');
        Route::post('/inline-edits/{inlineEdit}/approve', [ModerationController::class, 'approveInlineEdit'])->name('inline-edits.approve');
        Route::post('/inline-edits/{inlineEdit}/reject', [ModerationController::class, 'rejectInlineEdit'])->name('inline-edits.reject');
    });
});

require __DIR__.'/auth.php';
