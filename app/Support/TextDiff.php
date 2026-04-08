<?php

namespace App\Support;

use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;

final class TextDiff
{
    /** Enough for long TBWNN HTML chapters (~5k+ words) while bounding CPU. */
    public const PREVIEW_DIFF_MAX_TOTAL_BYTES = 600_000;

    public const PREVIEW_DIFF_MAX_OUTPUT_LINES = 4_000;

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
     * Normalize stored HTML / text so line-based diffs are not a single giant line (common with `><` blobs).
     */
    public static function normalizeForLineDiff(string $s): string
    {
        $s = str_replace(["\r\n", "\r"], "\n", $s);

        return (string) preg_replace('/></', ">\n<", $s);
    }

    /**
     * Collapse long runs of unchanged lines so previews stay scannable (large chapters).
     *
     * @param  list<array{text: string, kind: string}>  $lines
     * @return list<array{text: string, kind: string}>
     */
    public static function collapseLongSameRuns(
        array $lines,
        int $sameRunThreshold = 18,
        int $contextLines = 3,
    ): array {
        if ($sameRunThreshold < 4 || $contextLines < 1) {
            return $lines;
        }

        $out = [];
        $n = count($lines);
        $i = 0;

        while ($i < $n) {
            if ($lines[$i]['kind'] !== 'same') {
                $out[] = $lines[$i];
                $i++;

                continue;
            }

            $start = $i;
            while ($i < $n && $lines[$i]['kind'] === 'same') {
                $i++;
            }
            $runLen = $i - $start;
            $keepEdges = $contextLines * 2;

            if ($runLen <= max($sameRunThreshold, $keepEdges)) {
                for ($j = $start; $j < $i; $j++) {
                    $out[] = $lines[$j];
                }

                continue;
            }

            for ($j = $start; $j < $start + $contextLines; $j++) {
                $out[] = $lines[$j];
            }

            $hidden = $runLen - 2 * $contextLines;
            $out[] = [
                'text' => sprintf(
                    '… %d unchanged line(s) — same as published (hidden so you can focus on what changed).',
                    $hidden
                ),
                'kind' => 'warning',
            ];

            for ($j = $i - $contextLines; $j < $i; $j++) {
                $out[] = $lines[$j];
            }
        }

        return $out;
    }

    /**
     * Line-level diff for HTML display (no +/- prefixes; use row styling instead).
     *
     * @return array{lines: list<array{text: string, kind: string}>, truncated: bool, collapsed_same: bool}|null
     */
    public static function linesForDisplay(
        string $from,
        string $to,
        ?int $maxTotalBytes = null,
        ?int $maxLines = null,
    ): ?array {
        $maxTotalBytes ??= self::PREVIEW_DIFF_MAX_TOTAL_BYTES;
        $maxLines ??= self::PREVIEW_DIFF_MAX_OUTPUT_LINES;

        if (strlen($from) + strlen($to) > $maxTotalBytes) {
            return null;
        }

        $fromN = self::normalizeForLineDiff($from);
        $toN = self::normalizeForLineDiff($to);

        $builder = new UnifiedDiffOutputBuilder('', false);
        $differ = new Differ($builder);
        $chunks = $differ->diffToArray($fromN, $toN);

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

        $beforeCollapse = count($lines);
        $lines = self::collapseLongSameRuns($lines);
        $collapsedSame = count($lines) < $beforeCollapse;

        if ($truncated) {
            $lines[] = ['text' => '… Further changes hidden (open full A and B above to compare).', 'kind' => 'warning'];
        }

        return ['lines' => $lines, 'truncated' => $truncated, 'collapsed_same' => $collapsedSame];
    }
}
