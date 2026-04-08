<?php

namespace App\Support;

use App\Models\Chapter;
use App\Models\Edit;
use App\Models\InlineEdit;

/**
 * Builds a suggested merged manuscript from accepted chapter + paragraph edits (TBWNN admin).
 */
class TbwRevisionMergePreview
{
    /** @var non-empty-string */
    private const RS = "\x1E";

    /** @var non-empty-string */
    private const US = "\x1F";

    public static function mergedPlainText(Chapter $chapter): string
    {
        return self::buildMerged($chapter, false);
    }

    public static function mergedHighlightedHtml(Chapter $chapter): string
    {
        return self::markersToHtml(self::buildMerged($chapter, true));
    }

    private static function buildMerged(Chapter $chapter, bool $wrapAccepted): string
    {
        $lines = explode("\n", $chapter->content);

        $inlines = InlineEdit::query()
            ->where('chapter_id', $chapter->id)
            ->where('status', 'approved')
            ->orderBy('id')
            ->get();

        foreach ($inlines as $ie) {
            $idx = (int) $ie->paragraph_number;
            if (! array_key_exists($idx, $lines)) {
                continue;
            }
            $suggested = (string) $ie->suggested_text;
            $lines[$idx] = $wrapAccepted
                ? self::wrapAccepted($suggested, 'i'.$ie->id)
                : $suggested;
        }

        $text = implode("\n", $lines);

        $edits = Edit::query()
            ->where('chapter_id', $chapter->id)
            ->whereIn('status', ChapterLifecycle::ACCEPTED_EDIT_STATUSES)
            ->where('type', '!=', 'inline_edit')
            ->orderBy('id')
            ->get();

        foreach ($edits as $edit) {
            $orig = (string) $edit->original_text;
            $new = (string) $edit->edited_text;
            if ($orig === '') {
                continue;
            }
            $pos = strpos($text, $orig);
            if ($pos === false) {
                continue;
            }
            $replacement = $wrapAccepted ? self::wrapAccepted($new, 'e'.$edit->id) : $new;
            $text = substr_replace($text, $replacement, $pos, strlen($orig));
        }

        return $text;
    }

    private static function wrapAccepted(string $inner, string $tag): string
    {
        return self::RS.'S'.$tag.self::US.$inner.self::RS.'E'.$tag.self::US;
    }

    private static function markersToHtml(string $text): string
    {
        $escaped = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        return (string) preg_replace_callback(
            "/\x1ES([^\x1F]+)\x1F(.*?)\x1EE\\1\x1F/s",
            static fn (array $m) => '<mark class="bg-emerald-200/90 text-emerald-950 rounded px-0.5 font-semibold">'.$m[2].'</mark>',
            $escaped
        );
    }
}
