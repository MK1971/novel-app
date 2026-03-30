<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    protected $fillable = ['book_id', 'title', 'number', 'list_section', 'content', 'version', 'status', 'is_locked', 'is_archived'];

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

    /** Group A/B vote pairs (same section + number, different version). */
    public function votePairGroupKey(): string
    {
        return ($this->list_section ?? self::LIST_SECTION_CHAPTER).'::'.$this->number;
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
}
