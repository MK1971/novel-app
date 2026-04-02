<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

class Chapter extends Model
{
    use HasFactory;

    public const LIST_SECTION_COLD_OPEN = 'cold_open';

    public const LIST_SECTION_PROLOG = 'prolog';

    public const LIST_SECTION_CHAPTER = 'chapter';

    public const LIST_SECTION_EPILOG = 'epilog';

    /** @var list<string> */
    public const LIST_SECTIONS = [
        self::LIST_SECTION_COLD_OPEN,
        self::LIST_SECTION_PROLOG,
        self::LIST_SECTION_CHAPTER,
        self::LIST_SECTION_EPILOG,
    ];

    protected $fillable = [
        'book_id',
        'title',
        'number',
        'list_section',
        'content',
        'version',
        'status',
        'is_locked',
        'locked_at',
        'is_archived',
        'is_reader_archive_link',
        'published_at',
        'editing_closes_at',
        'editing_deadline_reminder_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'editing_closes_at' => 'datetime',
            'editing_deadline_reminder_sent_at' => 'datetime',
            'locked_at' => 'datetime',
            'is_reader_archive_link' => 'boolean',
        ];
    }

    /** Best-effort instant this row was locked (for reader copy). Falls back to updated_at if unset. */
    public function lockedAtForDisplay(): ?Carbon
    {
        if (! $this->is_locked) {
            return null;
        }

        return $this->locked_at ?? $this->updated_at;
    }

    /**
     * Order for reader and admin lists: cold open → prolog → chapters → epilog.
     */
    public static function listSectionOrderSql(): string
    {
        return "CASE list_section
            WHEN '".self::LIST_SECTION_COLD_OPEN."' THEN 0
            WHEN '".self::LIST_SECTION_PROLOG."' THEN 1
            WHEN '".self::LIST_SECTION_CHAPTER."' THEN 2
            WHEN '".self::LIST_SECTION_EPILOG."' THEN 3
            ELSE 2 END";
    }

    /** The Book With No Name — live manuscript rows (reader index / prev-next). */
    public function scopeForTbwReaderManuscript(Builder $query): Builder
    {
        return $query
            ->whereHas('book', fn (Builder $b) => $b->where('name', Book::NAME_THE_BOOK_WITH_NO_NAME))
            ->where('is_archived', false)
            ->where(function (Builder $q) {
                $q->whereNull('version')
                    ->orWhere('version', '')
                    ->orWhereRaw('LOWER(TRIM(version)) = ?', ['a']);
            });
    }

    /** TBWNN archived slots surfaced to readers as “previous versions.” */
    public function scopeForTbwReaderArchive(Builder $query): Builder
    {
        return $query
            ->whereHas('book', fn (Builder $b) => $b->where('name', Book::NAME_THE_BOOK_WITH_NO_NAME))
            ->where('is_archived', true)
            ->where('is_reader_archive_link', true);
    }

    public function wordCount(): int
    {
        $plain = trim(preg_replace('/\s+/u', ' ', strip_tags((string) $this->content)) ?? '');

        if ($plain === '') {
            return 0;
        }

        return str_word_count($plain);
    }

    /** Reading-time estimate at default adult fiction WPM. */
    public function estimatedReadingMinutes(int $wordsPerMinute = 200): int
    {
        $words = $this->wordCount();
        if ($words < 1) {
            return 1;
        }

        return max(1, (int) ceil($words / $wordsPerMinute));
    }

    public function listSectionLabel(): string
    {
        return match ($this->list_section ?? self::LIST_SECTION_CHAPTER) {
            self::LIST_SECTION_COLD_OPEN => 'Cold open',
            self::LIST_SECTION_PROLOG => 'Prolog',
            self::LIST_SECTION_EPILOG => 'Epilog',
            default => 'Chapter',
        };
    }

    /** Pill / badge text in chapter lists */
    public function listSectionBadge(): string
    {
        return match ($this->list_section ?? self::LIST_SECTION_CHAPTER) {
            self::LIST_SECTION_COLD_OPEN => 'Cold open',
            self::LIST_SECTION_PROLOG => 'Prolog',
            self::LIST_SECTION_EPILOG => 'Epilog',
            default => 'Chapter '.$this->number,
        };
    }

    /** Large faded marker in card corners */
    public function listSectionDecorativeMarker(): string
    {
        return match ($this->list_section ?? self::LIST_SECTION_CHAPTER) {
            self::LIST_SECTION_COLD_OPEN => 'CO',
            self::LIST_SECTION_PROLOG => 'P',
            self::LIST_SECTION_EPILOG => 'E',
            default => (string) $this->number,
        };
    }

    /** Reader chapter show header: "Chapter 1" / "Cold open" / … */
    public function headingPrefix(): string
    {
        return match ($this->list_section ?? self::LIST_SECTION_CHAPTER) {
            self::LIST_SECTION_COLD_OPEN => 'Cold open',
            self::LIST_SECTION_PROLOG => 'Prolog',
            self::LIST_SECTION_EPILOG => 'Epilog',
            default => 'Chapter '.$this->number,
        };
    }

    /** Title for display when `title` is empty (optional in admin upload). */
    public function displayTitle(): string
    {
        $t = trim((string) ($this->title ?? ''));

        return $t !== '' ? $t : 'Untitled';
    }

    /**
     * Insights / analytics: prefer the chapter title; if missing, use numeric slot (no cold open / prolog type labels).
     */
    public function insightDisplayLabel(): string
    {
        $t = trim((string) ($this->title ?? ''));
        if ($t !== '') {
            return $t;
        }

        return 'Chapter '.(string) $this->number;
    }

    /** Group A/B vote pairs (same section + number, different version). */
    public function votePairGroupKey(): string
    {
        return ($this->list_section ?? self::LIST_SECTION_CHAPTER).'::'.$this->number;
    }

    /**
     * Reader-facing “pieces” for stats: TBWNN uses the same single stream as /chapters (non-archived, version A / empty).
     * Peter Trull uses one count per voting slot (same list_section + number), not per A/B row.
     * Any other books: one row per non-archived chapter.
     */
    public static function logicalReaderPieceCount(bool $publishedOnly = false): int
    {
        $tbwnnId = Book::query()->where('name', Book::NAME_THE_BOOK_WITH_NO_NAME)->value('id');
        $ptId = Book::query()->where('name', Book::NAME_PETER_TRULL)->value('id');

        $applyLive = static function (Builder $q) use ($publishedOnly): void {
            $q->where('is_archived', false);
            if ($publishedOnly) {
                $q->where('status', 'published');
            }
        };

        $total = 0;

        if ($tbwnnId) {
            $q = static::query()->where('book_id', $tbwnnId);
            $applyLive($q);
            $q->where(function (Builder $qq) {
                $qq->whereNull('version')
                    ->orWhere('version', '')
                    ->orWhereRaw('LOWER(TRIM(version)) = ?', ['a']);
            });
            $total += $q->count();
        }

        if ($ptId) {
            $q = static::query()->where('book_id', $ptId);
            $applyLive($q);
            $total += $q->get(['list_section', 'number'])
                ->unique(fn (self $c) => ($c->list_section ?? self::LIST_SECTION_CHAPTER).'|'.$c->number)
                ->count();
        }

        $otherIds = Book::query()
            ->pluck('id')
            ->diff(collect([$tbwnnId, $ptId])->filter());

        if ($otherIds->isNotEmpty()) {
            $q = static::query()->whereIn('book_id', $otherIds);
            $applyLive($q);
            $total += $q->count();
        }

        return $total;
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function edits(): HasMany
    {
        return $this->hasMany(Edit::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function readingProgress(): HasMany
    {
        return $this->hasMany(ReadingProgress::class);
    }

    public function statistics(): HasOne
    {
        return $this->hasOne(ChapterStatistic::class);
    }

    public function paragraphReactions(): HasMany
    {
        return $this->hasMany(ParagraphReaction::class);
    }

    public function activityFeed(): HasMany
    {
        return $this->hasMany(ActivityFeed::class);
    }

    public function isPastEditingWindow(): bool
    {
        return $this->editing_closes_at !== null && now()->greaterThan($this->editing_closes_at);
    }

    /** Paid manuscript suggestions (TBWNN index inline) allowed when chapter is open and within editing window. */
    public function manuscriptPaidEditsOpen(): bool
    {
        if ($this->is_locked) {
            return false;
        }

        return ! $this->isPastEditingWindow();
    }
}
