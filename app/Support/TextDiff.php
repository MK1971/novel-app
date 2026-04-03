<?php

namespace App\Support;

use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;

final class TextDiff
{
    /**
     * Unified diff (Version A → B). Returns null if combined length exceeds limit (avoid huge CPU).
     */
    public static function unified(string $from, string $to, int $maxTotalBytes = 120_000): ?string
    {
        if (strlen($from) + strlen($to) > $maxTotalBytes) {
            return null;
        }

        $builder = new UnifiedDiffOutputBuilder("--- Version A\n+++ Version B\n", false);
        $differ = new Differ($builder);

        return $differ->diff($from, $to);
    }

    /**
     * Line-level diff for HTML display (no +/- prefixes; use row styling instead).
     *
     * @return array{lines: list<array{text: string, kind: string}>, truncated: bool}|null
     */
    public static function linesForDisplay(
        string $from,
        string $to,
        int $maxTotalBytes = 120_000,
        int $maxLines = 500,
    ): ?array {
        if (strlen($from) + strlen($to) > $maxTotalBytes) {
            return null;
        }

        $builder = new UnifiedDiffOutputBuilder('', false);
        $differ = new Differ($builder);
        $chunks = $differ->diffToArray($from, $to);

        $lines = [];
        $truncated = false;

        foreach ($chunks as [$text, $type]) {
            if (count($lines) >= $maxLines) {
                $truncated = true;
                break;
            }

            if ($type === Differ::DIFF_LINE_END_WARNING) {
                $lines[] = ['text' => trim($text, "\r\n"), 'kind' => 'warning'];

                continue;
            }

            $kind = match ($type) {
                Differ::ADDED => 'added',
                Differ::REMOVED => 'removed',
                default => 'same',
            };

            $lines[] = ['text' => $text, 'kind' => $kind];
        }

        if ($truncated) {
            $lines[] = ['text' => '… Further changes hidden (open full A and B above to compare).', 'kind' => 'warning'];
        }

        return ['lines' => $lines, 'truncated' => $truncated];
    }
}
