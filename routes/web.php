<?php

use App\Http\Controllers\AchievementController;
use App\Http\Controllers\Admin\BlogPostController as AdminBlogPostController;
use App\Http\Controllers\Admin\ChapterController as AdminChapterController;
use App\Http\Controllers\Admin\DonationReportController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\EditController;
use App\Http\Controllers\EditDiffPreviewController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\InlineEditController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\ModerationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ParagraphReactionController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentHistoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileSubmissionVisibilityController;
use App\Http\Controllers\PublicProfileAbuseController;
use App\Http\Controllers\PublicEditsController;
use App\Http\Controllers\PublicProfileController;
use App\Http\Controllers\RssFeedController;
use App\Http\Controllers\VoteController;
use App\Models\Chapter;
use App\Models\Edit;
use App\Models\InlineEdit;
use App\Models\Payment;
use App\Models\User;
use App\Support\AchievementUnlock;
use App\Support\ChapterLifecycle;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

    $chaptersLiveCount = Chapter::logicalReaderPieceCount();

    $landingStats = [
        'contributors' => $abbrevInt($contributorsCount),
        'edits_accepted' => $abbrevInt($editsAcceptedCount),
        'chapters_live' => (string) $chaptersLiveCount,
        'prize_pool' => config('marketing.landing_prize_pool_display'),
    ];

    $landingStatsQuiet = (bool) config('marketing.landing_soft_stats_when_empty', true)
        && $contributorsCount === 0
        && $editsAcceptedCount === 0
        && $chaptersLiveCount === 0;

    $guestFirstVisitNav = false;
    if (! auth()->check()) {
        $guestFirstVisitNav = ! session()->has('landing_guest_seen');
        session(['landing_guest_seen' => true]);
    }

    $previewChapter = ChapterLifecycle::latestOpenTbwChapter()
        ?? Chapter::query()
            ->where('is_archived', false)
            ->orderByDesc('id')
            ->first();

    $previewExcerpt = null;
    $previewExcerptLines = [];
    $previewUrgencyLabel = null;
    if ($previewChapter) {
        $lines = preg_split("/\r\n|\r|\n/", (string) $previewChapter->content) ?: [];
        $nonEmptyLines = [];
        foreach ($lines as $line) {
            $t = trim((string) $line);
            if ($t !== '') {
                $nonEmptyLines[] = $t;
            }
            if (count($nonEmptyLines) >= 2) {
                break;
            }
        }
        if ($nonEmptyLines !== []) {
            $previewExcerptLines = array_map(static function (string $line): string {
                return mb_strlen($line) > 180 ? mb_substr($line, 0, 180).'...' : $line;
            }, $nonEmptyLines);

            $firstNonEmpty = $nonEmptyLines[0];
            $previewExcerpt = mb_strlen($firstNonEmpty) > 220 ? mb_substr($firstNonEmpty, 0, 220).'...' : $firstNonEmpty;
        }

        if (! $previewChapter->is_locked && $previewChapter->editing_closes_at) {
            $previewUrgencyLabel = 'Window closes '.$previewChapter->editing_closes_at->diffForHumans();
        } elseif (! $previewChapter->is_locked && $previewChapter->isPilotManuscriptChapter()) {
            $cap = max(1, (int) config('tbwnn.pilot.close_after_accepted_edits', 50));
            $previewUrgencyLabel = 'Pilot target: '.$previewChapter->pilotAcceptedEditsTotal().'/'.$cap.' accepted edits';
        }
    }

    $latestChapterEdit = Edit::query()
        ->whereIn('status', $acceptedEditStatuses)
        ->where('type', '!=', 'inline_edit')
        ->where('show_in_public_feed', true)
        ->with(['user:id,name', 'chapter'])
        ->orderByDesc('updated_at')
        ->first();

    $latestInlineEdit = InlineEdit::query()
        ->where('status', 'approved')
        ->where('show_in_public_feed', true)
        ->with(['user:id,name', 'chapter'])
        ->orderByDesc('updated_at')
        ->first();

    $latestReplacement = null;
    $latestCandidate = null;
    if ($latestChapterEdit && $latestInlineEdit) {
        $latestCandidate = $latestChapterEdit->updated_at?->greaterThan($latestInlineEdit->updated_at) ? $latestChapterEdit : $latestInlineEdit;
    } else {
        $latestCandidate = $latestChapterEdit ?: $latestInlineEdit;
    }
    if ($latestCandidate instanceof Edit) {
        $latestReplacement = [
            'kind' => 'chapter',
            'user_name' => $latestCandidate->user?->name,
            'chapter_heading' => $latestCandidate->chapter?->readerHeadingLine(),
            'chapter_url' => $latestCandidate->chapter ? route('chapters.show', $latestCandidate->chapter) : null,
            'original' => (string) ($latestCandidate->original_text ?? ''),
            'suggested' => (string) ($latestCandidate->edited_text ?? ''),
            'at' => $latestCandidate->updated_at,
        ];
    }
    if ($latestCandidate instanceof InlineEdit) {
        $latestReplacement = [
            'kind' => 'inline',
            'user_name' => $latestCandidate->user?->name,
            'chapter_heading' => $latestCandidate->chapter?->readerHeadingLine(),
            'chapter_url' => $latestCandidate->chapter ? route('chapters.show', $latestCandidate->chapter) : null,
            'original' => (string) ($latestCandidate->original_text ?? ''),
            'suggested' => (string) ($latestCandidate->suggested_text ?? ''),
            'at' => $latestCandidate->updated_at,
        ];
    }

    $recentChapterReplacements = Edit::query()
        ->whereIn('status', $acceptedEditStatuses)
        ->where('type', '!=', 'inline_edit')
        ->where('show_in_public_feed', true)
        ->with(['user:id,name', 'chapter'])
        ->orderByDesc('updated_at')
        ->limit(4)
        ->get()
        ->map(function (Edit $edit): array {
            return [
                'kind' => 'chapter',
                'user_name' => $edit->user?->name,
                'chapter_heading' => $edit->chapter?->readerHeadingLine(),
                'chapter_url' => $edit->chapter ? route('chapters.show', $edit->chapter) : null,
                'original' => (string) ($edit->original_text ?? ''),
                'suggested' => (string) ($edit->edited_text ?? ''),
                'at' => $edit->updated_at,
            ];
        });

    $recentInlineReplacements = InlineEdit::query()
        ->where('status', 'approved')
        ->where('show_in_public_feed', true)
        ->with(['user:id,name', 'chapter'])
        ->orderByDesc('updated_at')
        ->limit(4)
        ->get()
        ->map(function (InlineEdit $edit): array {
            return [
                'kind' => 'inline',
                'user_name' => $edit->user?->name,
                'chapter_heading' => $edit->chapter?->readerHeadingLine(),
                'chapter_url' => $edit->chapter ? route('chapters.show', $edit->chapter) : null,
                'original' => (string) ($edit->original_text ?? ''),
                'suggested' => (string) ($edit->suggested_text ?? ''),
                'at' => $edit->updated_at,
            ];
        });

    $recentAcceptedReplacements = $recentChapterReplacements
        ->concat($recentInlineReplacements)
        ->sortByDesc('at')
        ->take(3)
        ->values()
        ->map(function (array $item): array {
            $trim = static function (string $value): string {
                $value = trim($value);
                if ($value === '') {
                    return '';
                }

                return mb_strlen($value) > 120 ? mb_substr($value, 0, 120).'...' : $value;
            };

            $item['original'] = $trim((string) ($item['original'] ?? ''));
            $item['suggested'] = $trim((string) ($item['suggested'] ?? ''));

            return $item;
        });

    return view('welcome', [
        'landingStats' => $landingStats,
        'landingStatsQuiet' => $landingStatsQuiet,
        'previewChapter' => $previewChapter,
        'previewExcerpt' => $previewExcerpt,
        'previewExcerptLines' => $previewExcerptLines,
        'previewUrgencyLabel' => $previewUrgencyLabel,
        'latestReplacement' => $latestReplacement,
        'recentAcceptedReplacements' => $recentAcceptedReplacements,
        'guestFirstVisitNav' => $guestFirstVisitNav,
    ]);
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

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

Route::get('/privacy', function () {
    return view('privacy');
})->name('privacy');

Route::get('/terms', function () {
    return view('terms');
})->name('terms');

Route::get('/legal', fn () => view('legal.index'))->name('legal.index');
Route::get('/legal/refunds', fn () => view('legal.refunds'))->name('legal.refunds');
Route::get('/legal/community', fn () => view('legal.community'))->name('legal.community');
Route::get('/legal/cookies', fn () => view('legal.cookies'))->name('legal.cookies');

Route::get('/prizes', function () {
    return view('prizes');
})->name('prizes');

Route::get('/feed/chapters.xml', [RssFeedController::class, 'tbwChapters'])->name('feed.chapters');

Route::get('/chapters', [ChapterController::class, 'index'])->name('chapters.index');
Route::get('/chapters/{chapter}', [ChapterController::class, 'show'])->name('chapters.show');
Route::post('/chapters/{chapter}/track-progress', [ChapterController::class, 'trackProgress'])->middleware('auth')->name('chapters.track-progress');
Route::get('/chapters/{chapter}/get-progress', [ChapterController::class, 'getProgress'])->middleware('auth')->name('chapters.get-progress');

Route::get('/leaderboard', LeaderboardController::class)->name('leaderboard');

Route::get('/hall-of-fame', function () {
    $adminEmail = (string) config('app.admin_email', 'admin@example.com');
    $excludeUserId = User::query()->where('email', $adminEmail)->value('id');
    $hiddenUserIds = User::query()
        ->where('leaderboard_visible', false)
        ->pluck('id')
        ->all();

    $chapterAccepted = DB::table('edits')
        ->select('user_id', DB::raw('COUNT(*) as accepted_total'))
        ->where('type', '!=', 'inline_edit')
        ->whereIn('status', ChapterLifecycle::ACCEPTED_EDIT_STATUSES)
        ->groupBy('user_id');

    $inlineAccepted = DB::table('inline_edits')
        ->select('user_id', DB::raw('COUNT(*) as accepted_total'))
        ->where('status', 'approved')
        ->groupBy('user_id');

    $query = User::query()
        ->select('users.id', 'users.name', 'users.public_profile_enabled', 'users.public_slug', 'users.points')
        ->selectRaw('(COALESCE(ch.accepted_total, 0) + COALESCE(ia.accepted_total, 0)) as accepted_total')
        ->leftJoinSub($chapterAccepted, 'ch', function ($join): void {
            $join->on('ch.user_id', '=', 'users.id');
        })
        ->leftJoinSub($inlineAccepted, 'ia', function ($join): void {
            $join->on('ia.user_id', '=', 'users.id');
        })
        ->where('users.leaderboard_visible', true)
        ->whereRaw('(COALESCE(ch.accepted_total, 0) + COALESCE(ia.accepted_total, 0)) > 0')
        ->orderByDesc('accepted_total')
        ->orderByDesc('users.points')
        ->orderBy('users.id');

    if ($excludeUserId) {
        $query->where('users.id', '!=', $excludeUserId);
    }
    if ($hiddenUserIds !== []) {
        $query->whereNotIn('users.id', $hiddenUserIds);
    }

    $hallOfFameUsers = $query->limit(50)->get();

    return view('hall-of-fame', [
        'hallOfFameUsers' => $hallOfFameUsers,
    ]);
})->name('hall-of-fame');

Route::get('/people/{slug}', [PublicProfileController::class, 'show'])
    ->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*')
    ->name('profile.public');

Route::get('/vote', [VoteController::class, 'index'])->name('vote.index');
Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
Route::get('/analytics/export', [AnalyticsController::class, 'exportCsv'])->name('analytics.export');
Route::post('/analytics/event', function (\Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'event' => 'required|string|max:80',
        'context' => 'nullable|array',
    ]);

    Log::info('frontend.analytics.event', [
        'event' => $validated['event'],
        'context' => $validated['context'] ?? [],
        'url' => $request->headers->get('referer'),
        'ip' => $request->ip(),
        'user_id' => auth()->id(),
        'user_agent' => (string) $request->userAgent(),
    ]);

    return response()->json(['ok' => true]);
})->middleware('throttle:120,1')->name('analytics.event');
Route::get('/achievements', [AchievementController::class, 'index'])->name('achievements.index');
Route::get('/achievements/{achievement}', [AchievementController::class, 'show'])->name('achievements.show');
Route::get('/archive/chapters', [ArchiveController::class, 'chapters'])->name('archive.chapters');
Route::get('/archive/rounds', [ArchiveController::class, 'rounds'])->name('archive.rounds');

Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback.index');
Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
Route::get('/edits/public', [PublicEditsController::class, 'index'])->name('edits.public');
Route::post('/payment/donation/webhook', [PaymentController::class, 'donationWebhook'])
    ->withoutMiddleware([VerifyCsrfToken::class])
    ->name('payment.donation.webhook');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        AchievementUnlock::syncForUser($user);

        $achievements = \App\Models\Achievement::all();
        $userAchievements = $user->achievements()->pluck('achievement_id')->toArray();
        $progressByAchievementId = [];
        foreach ($achievements as $achievement) {
            $progressByAchievementId[$achievement->id] = AchievementUnlock::currentProgressToward($user, $achievement);
        }
        $canVote = Payment::query()
            ->where('user_id', $user->id)
            ->withAvailableVoteCredit()
            ->exists();

        $fundingGoalCents = (int) config('marketing.funding_goal_cents', 5000000);
        $contributionCents = (int) Payment::query()
            ->where('status', 'completed')
            ->where('purpose', 'donation')
            ->sum('amount_cents');
        $competitorCents = (int) Payment::query()
            ->where('status', 'completed')
            ->where('purpose', 'edit_fee')
            ->sum('amount_cents');
        $totalCents = $contributionCents + $competitorCents;
        $fundingProgressPercent = $fundingGoalCents > 0
            ? min(100, (int) round(($totalCents / $fundingGoalCents) * 100))
            : 0;

        $firstOpenTbwChapter = ChapterLifecycle::latestOpenTbwChapter();

        $adminEmail = (string) config('app.admin_email', 'admin@example.com');
        $excludeUserId = User::query()->where('email', $adminEmail)->value('id');
        $hiddenUserIds = User::query()
            ->where('leaderboard_visible', false)
            ->pluck('id')
            ->all();

        $chapterAccepted = DB::table('edits')
            ->select('user_id', DB::raw('COUNT(*) as accepted_total'))
            ->where('type', '!=', 'inline_edit')
            ->whereIn('status', ChapterLifecycle::ACCEPTED_EDIT_STATUSES)
            ->groupBy('user_id');

        $inlineAccepted = DB::table('inline_edits')
            ->select('user_id', DB::raw('COUNT(*) as accepted_total'))
            ->where('status', 'approved')
            ->groupBy('user_id');

        $acceptedRankQuery = User::query()
            ->select('users.id')
            ->selectRaw('(COALESCE(ch.accepted_total, 0) + COALESCE(ia.accepted_total, 0)) as accepted_total')
            ->leftJoinSub($chapterAccepted, 'ch', function ($join): void {
                $join->on('ch.user_id', '=', 'users.id');
            })
            ->leftJoinSub($inlineAccepted, 'ia', function ($join): void {
                $join->on('ia.user_id', '=', 'users.id');
            })
            ->where('users.leaderboard_visible', true)
            ->whereRaw('(COALESCE(ch.accepted_total, 0) + COALESCE(ia.accepted_total, 0)) > 0')
            ->orderByDesc('accepted_total')
            ->orderByDesc('users.points')
            ->orderBy('users.id');

        if ($excludeUserId) {
            $acceptedRankQuery->where('users.id', '!=', $excludeUserId);
        }
        if ($hiddenUserIds !== []) {
            $acceptedRankQuery->whereNotIn('users.id', $hiddenUserIds);
        }

        $acceptedRankedIds = $acceptedRankQuery->pluck('users.id')->values();
        $acceptedRankPos = $acceptedRankedIds->search(fn ($id) => (int) $id === (int) $user->id);
        $acceptedPrizeRank = $acceptedRankPos === false ? null : $acceptedRankPos + 1;
        $acceptedReplacementsTotal = $user->acceptedChapterAndParagraphEditCount();

        $liveRecognitionBadges = [
            [
                'label' => '#1 Cover leader',
                'detail' => 'Current cover-credit holder',
                'active' => $acceptedPrizeRank === 1,
            ],
            [
                'label' => 'Top 3 Podium',
                'detail' => 'Current placement prize holder',
                'active' => $acceptedPrizeRank !== null && $acceptedPrizeRank <= 3,
            ],
            [
                'label' => 'Top 10 Signed print',
                'detail' => 'Within signed first print range',
                'active' => $acceptedPrizeRank !== null && $acceptedPrizeRank <= 10,
            ],
            [
                'label' => 'Top 50 Hall of Fame',
                'detail' => 'Within Editor Hall of Fame range',
                'active' => $acceptedPrizeRank !== null && $acceptedPrizeRank <= 50,
            ],
        ];

        $recognitionMilestones = $achievements
            ->where('requirement_type', 'accepted_rank_at_or_better')
            ->map(function (\App\Models\Achievement $achievement) use ($userAchievements) {
                return [
                    'name' => $achievement->name,
                    'icon' => $achievement->icon_emoji,
                    'description' => $achievement->description,
                    'requirement' => $achievement->requirementLabel(),
                    'rank_target' => (int) $achievement->requirement_value,
                    'unlocked' => in_array($achievement->id, $userAchievements, true),
                ];
            })
            ->sortBy('rank_target')
            ->values();

        return view('dashboard', compact(
            'achievements',
            'userAchievements',
            'progressByAchievementId',
            'canVote',
            'firstOpenTbwChapter',
            'fundingGoalCents',
            'contributionCents',
            'competitorCents',
            'totalCents',
            'fundingProgressPercent',
            'acceptedPrizeRank',
            'acceptedReplacementsTotal',
            'liveRecognitionBadges',
            'recognitionMilestones'
        ));
    })->name('dashboard');

    Route::post('/onboarding/dismiss', function () {
        auth()->user()->update(['onboarding_completed_at' => now()]);

        return redirect()->route('dashboard');
    })->name('onboarding.dismiss');

    Route::post('/dev/tools/reset-all', function () {
        abort_unless(app()->isLocal(), 404);

        Artisan::call('db:reset-app-data', ['--force' => true]);

        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login')->with('warning', 'Local reset complete: all app data cleared and admin reseeded. Please sign in again.');
    })->middleware('admin')->name('dev.tools.reset-all');

    Route::post('/dev/tools/reset-content', function () {
        abort_unless(app()->isLocal(), 404);

        Artisan::call('db:reset-content-keep-users', ['--force' => true]);

        return redirect()->route('dashboard')->with('warning', 'Local reset complete: content cleared, users preserved.');
    })->middleware('admin')->name('dev.tools.reset-content');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/payments', PaymentHistoryController::class)->name('profile.payments');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    // apple: same as auth routes — useful when APPLE_SIGN_IN_ENABLED=true or legacy linked accounts.
    Route::delete('/profile/social/{provider}', [ProfileController::class, 'disconnectSocial'])
        ->whereIn('provider', ['google', 'apple'])
        ->name('profile.social.disconnect');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/public-settings', [ProfileController::class, 'updatePublicSettings'])->name('profile.public-settings.update');
    Route::patch('/profile/submissions/{kind}/{id}/visibility', [ProfileSubmissionVisibilityController::class, 'update'])
        ->whereIn('kind', ['chapter', 'inline'])
        ->name('profile.submissions.visibility');
    Route::post('/people/{slug}/report', [PublicProfileAbuseController::class, 'report'])
        ->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*')
        ->name('profile.public.report');
    Route::post('/people/{slug}/block', [PublicProfileAbuseController::class, 'block'])
        ->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*')
        ->name('profile.public.block');
    Route::delete('/people/{slug}/block', [PublicProfileAbuseController::class, 'unblock'])
        ->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*')
        ->name('profile.public.unblock');
    Route::delete('/profile/blocks/{blocked}', [PublicProfileAbuseController::class, 'unblockByUser'])
        ->name('profile.blocks.destroy');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/edits/preview-diff', EditDiffPreviewController::class)->name('edits.preview-diff');
    Route::post('/edits/public/feedback', [PublicEditsController::class, 'storeFeedback'])->name('edits.public.feedback');

    Route::post('/payment/checkout', [PaymentController::class, 'checkout'])->name('payment.checkout');
    Route::delete('/payment/queue/{editId}', [PaymentController::class, 'removeQueuedEdit'])->name('payment.queue.remove');
    Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
    Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
    Route::post('/payment/donation/checkout', [PaymentController::class, 'donationCheckout'])->name('payment.donation.checkout');
    Route::get('/payment/donation/cancel', [PaymentController::class, 'donationCancel'])->name('payment.donation.cancel');
    Route::get('/payment/donation/success', [PaymentController::class, 'donationSuccess'])->name('payment.donation.success');

    Route::get('/chapters/{chapterId}/edit', [EditController::class, 'create'])->name('edits.create');
    Route::post('/edits', [EditController::class, 'store'])->name('edits.store');
    Route::post('/vote/{chapter}', [VoteController::class, 'store'])->name('vote.store');
    Route::post('/chapters/{chapter}/inline-edit', [InlineEditController::class, 'store'])->name('inline-edit.store');
    Route::post('/paragraphs/{chapter}/react', [ParagraphReactionController::class, 'store'])->name('paragraph-reaction.store');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

    Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/blog', [AdminBlogPostController::class, 'index'])->name('blog.index');
        Route::get('/blog/create', [AdminBlogPostController::class, 'create'])->name('blog.create');
        Route::post('/blog', [AdminBlogPostController::class, 'store'])->name('blog.store');
        Route::get('/blog/{blogPost}/edit', [AdminBlogPostController::class, 'edit'])->name('blog.edit');
        Route::get('/blog/{blogPost}/preview', [AdminBlogPostController::class, 'preview'])->name('blog.preview');
        Route::post('/blog/{blogPost}/publish-now', [AdminBlogPostController::class, 'publishNow'])->name('blog.publish-now');
        Route::put('/blog/{blogPost}', [AdminBlogPostController::class, 'update'])->name('blog.update');
        Route::delete('/blog/{blogPost}', [AdminBlogPostController::class, 'destroy'])->name('blog.destroy');

        Route::get('/edits', [ModerationController::class, 'index'])->name('edits.index');
        Route::post('/edits/{edit}/approve', [ModerationController::class, 'approve'])->name('edits.approve');
        Route::post('/edits/{edit}/reject', [ModerationController::class, 'reject'])->name('edits.reject');

        Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

        Route::get('/chapters', [AdminChapterController::class, 'index'])->name('chapters.index');
        Route::post('/chapters/story', [AdminChapterController::class, 'storeStoryChapter'])->name('chapters.store-story');
        Route::post('/chapters/story/revision', [AdminChapterController::class, 'publishStoryRevision'])->name('chapters.publish-story-revision');
        Route::post('/chapters/story/{chapter}/close-without-merge', [AdminChapterController::class, 'closeStoryWithoutMergedUpload'])->name('chapters.close-story-without-merge');
        Route::post('/chapters/story/{chapter}/extend-window', [AdminChapterController::class, 'extendEditingWindow'])->name('chapters.extend-editing-window');
        Route::post('/chapters/peter-trull', [AdminChapterController::class, 'storePeterTrullChapter'])->name('chapters.store-peter-trull');
        Route::delete('/chapters/{chapter}', [AdminChapterController::class, 'destroy'])->name('chapters.destroy');
        Route::post('/chapters/{chapter}/toggle-lock', [AdminChapterController::class, 'toggleLock'])->name('chapters.toggle-lock');
        Route::post('/chapters/{chapter}/archive', [AdminChapterController::class, 'archive'])->name('chapters.archive');

        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');

        Route::get('/feedback', [FeedbackController::class, 'adminIndex'])->name('feedback.index');
        Route::get('/donations', [DonationReportController::class, 'index'])->name('donations.index');
        Route::get('/donations/export', [DonationReportController::class, 'exportCsv'])->name('donations.export');

        Route::get('/inline-edits', [ModerationController::class, 'inlineEdits'])->name('inline-edits.index');
        Route::post('/inline-edits/{inlineEdit}/approve', [ModerationController::class, 'approveInlineEdit'])->name('inline-edits.approve');
        Route::post('/inline-edits/{inlineEdit}/reject', [ModerationController::class, 'rejectInlineEdit'])->name('inline-edits.reject');
    });
});

require __DIR__.'/auth.php';
