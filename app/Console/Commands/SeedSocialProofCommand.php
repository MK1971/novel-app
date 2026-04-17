<?php

namespace App\Console\Commands;

use App\Models\Book;
use App\Models\Chapter;
use App\Models\ChapterStatistic;
use App\Models\Edit;
use App\Models\InlineEdit;
use App\Models\User;
use App\Support\AchievementUnlock;
use App\Support\ChapterLifecycle;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class SeedSocialProofCommand extends Command
{
    protected $signature = 'demo:seed-social-proof
        {--force : Seed demo users and edits without confirmation}';

    protected $description = 'Seed realistic demo users plus accepted/rejected edits for social proof';

    public function handle(): int
    {
        if (! $this->option('force') && ! $this->confirm('Seed demo social-proof users and edits?')) {
            $this->components->info('Aborted.');

            return self::SUCCESS;
        }

        $chapters = $this->seedableChapters();
        if ($chapters->isEmpty()) {
            $this->components->error('No chapters found to attach demo edits.');

            return self::FAILURE;
        }

        $profiles = [
            ['name' => 'Nora Vale', 'email' => 'nora.vale.demo@whatsmybookname.local', 'slug' => 'nora-vale'],
            ['name' => 'Ilan Mercer', 'email' => 'ilan.mercer.demo@whatsmybookname.local', 'slug' => 'ilan-mercer'],
            ['name' => 'Mika Rowe', 'email' => 'mika.rowe.demo@whatsmybookname.local', 'slug' => 'mika-rowe'],
            ['name' => 'Daria Flint', 'email' => 'daria.flint.demo@whatsmybookname.local', 'slug' => 'daria-flint'],
            ['name' => 'Oren Pike', 'email' => 'oren.pike.demo@whatsmybookname.local', 'slug' => 'oren-pike'],
            ['name' => 'Lena Crow', 'email' => 'lena.crow.demo@whatsmybookname.local', 'slug' => 'lena-crow'],
        ];

        $users = collect($profiles)->map(function (array $profile): User {
            return User::updateOrCreate(
                ['email' => $profile['email']],
                [
                    'name' => $profile['name'],
                    'password' => Str::password(32),
                    'points' => 0,
                    'public_profile_enabled' => true,
                    'public_slug' => $profile['slug'],
                    'leaderboard_visible' => true,
                    'profile_indexable' => true,
                    'email_verified_at' => Carbon::now(),
                    'onboarding_completed_at' => Carbon::now(),
                ]
            );
        });

        $userIds = $users->pluck('id')->all();
        Edit::query()->whereIn('user_id', $userIds)->where('type', '!=', 'inline_edit')->delete();
        InlineEdit::query()->whereIn('user_id', $userIds)->delete();

        $statusCycle = [
            ['status' => 'accepted_full', 'points' => 2, 'type' => 'writing'],
            ['status' => 'accepted_partial', 'points' => 1, 'type' => 'phrase'],
            ['status' => 'rejected', 'points' => 0, 'type' => 'phrase'],
            ['status' => 'accepted_full', 'points' => 2, 'type' => 'writing'],
            ['status' => 'accepted_partial', 'points' => 1, 'type' => 'phrase'],
            ['status' => 'rejected', 'points' => 0, 'type' => 'writing'],
        ];

        foreach ($users as $idx => $user) {
            $chapter = $chapters[$idx % $chapters->count()];
            $seed = $statusCycle[$idx % count($statusCycle)];
            [$originalLine, $editedLine] = $this->linePair($chapter, $idx);

            Edit::query()->create([
                'user_id' => $user->id,
                'chapter_id' => $chapter->id,
                'type' => $seed['type'],
                'original_text' => $originalLine,
                'edited_text' => $editedLine,
                'status' => $seed['status'],
                'show_in_public_feed' => true,
                'points_awarded' => $seed['points'],
                'created_at' => now()->subDays(7 - $idx),
                'updated_at' => now()->subDays(6 - $idx),
            ]);

            InlineEdit::query()->create([
                'chapter_id' => $chapter->id,
                'user_id' => $user->id,
                'paragraph_number' => ($idx % 3) + 1,
                'original_text' => $originalLine,
                'suggested_text' => $editedLine.' (inline)',
                'reason' => 'Demo social proof seed',
                'status' => $idx % 4 === 0 ? 'rejected' : 'approved',
                'moderation_outcome' => $idx % 2 === 0 ? InlineEdit::OUTCOME_PARTIAL : InlineEdit::OUTCOME_FULL,
                'admin_notes' => 'Seeded demo moderation outcome',
                'show_in_public_feed' => true,
                'created_at' => now()->subDays(5 - min(4, $idx)),
                'updated_at' => now()->subDays(4 - min(4, $idx)),
            ]);
        }

        foreach ($users as $user) {
            $chapterPoints = Edit::query()
                ->where('user_id', $user->id)
                ->where('type', '!=', 'inline_edit')
                ->whereIn('status', ChapterLifecycle::ACCEPTED_EDIT_STATUSES)
                ->sum('points_awarded');

            $inlinePoints = InlineEdit::query()
                ->where('user_id', $user->id)
                ->where('status', 'approved')
                ->get()
                ->sum(function (InlineEdit $inline): int {
                    return $inline->moderation_outcome === InlineEdit::OUTCOME_PARTIAL ? 1 : 2;
                });

            $user->forceFill(['points' => (int) $chapterPoints + (int) $inlinePoints])->save();
            AchievementUnlock::syncForUser($user);
        }

        $chapterIds = $chapters->pluck('id')->all();
        foreach ($chapterIds as $chapterId) {
            $total = Edit::query()
                ->where('chapter_id', $chapterId)
                ->where('type', '!=', 'inline_edit')
                ->count();
            $accepted = Edit::query()
                ->where('chapter_id', $chapterId)
                ->where('type', '!=', 'inline_edit')
                ->whereIn('status', ChapterLifecycle::ACCEPTED_EDIT_STATUSES)
                ->count();
            $rejected = Edit::query()
                ->where('chapter_id', $chapterId)
                ->where('type', '!=', 'inline_edit')
                ->where('status', 'rejected')
                ->count();

            ChapterStatistic::query()->updateOrCreate(
                ['chapter_id' => $chapterId],
                [
                    'total_reads' => 0,
                    'total_edits' => $total,
                    'accepted_edits' => $accepted,
                    'rejected_edits' => $rejected,
                    'total_votes' => 0,
                ]
            );
        }

        $this->components->info('Seeded demo social-proof users, leaderboard points, and accepted/rejected edits.');
        $this->components->info('Use route pages /leaderboard, /, and /edits/public to verify.');

        return self::SUCCESS;
    }

    private function seedableChapters()
    {
        $tbwBookId = Book::query()->where('name', Book::NAME_THE_BOOK_WITH_NO_NAME)->value('id');

        $query = Chapter::query()
            ->where('is_archived', false)
            ->orderBy('id');

        if ($tbwBookId) {
            $query->where('book_id', $tbwBookId);
        }

        $rows = $query->limit(4)->get();
        if ($rows->isNotEmpty()) {
            return $rows;
        }

        return Chapter::query()->where('is_archived', false)->orderBy('id')->limit(4)->get();
    }

    /**
     * @return array{0:string,1:string}
     */
    private function linePair(Chapter $chapter, int $seedIndex): array
    {
        $lines = collect(preg_split("/\r\n|\r|\n/", (string) $chapter->content) ?: [])
            ->map(fn (string $line) => trim($line))
            ->filter(fn (string $line) => $line !== '')
            ->values();

        $fallbackOriginal = 'The door was already open.';
        $fallbackEdited = 'The door was already open, and he knew someone else had been there first.';

        if ($lines->isEmpty()) {
            return [$fallbackOriginal, $fallbackEdited];
        }

        $original = (string) $lines[$seedIndex % $lines->count()];
        $edited = match ($seedIndex % 4) {
            0 => $original.' He noticed the silence before the fear.',
            1 => 'He paused, then stepped inside without looking back.',
            2 => 'The line felt wrong to him, so he rewrote it in his own voice.',
            default => 'By the time he crossed the threshold, the outcome had already shifted.',
        };

        return [$original, $edited];
    }
}
