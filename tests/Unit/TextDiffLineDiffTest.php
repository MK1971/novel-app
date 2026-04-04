<?php

namespace Tests\Unit;

use App\Support\TextDiff;
use PHPUnit\Framework\TestCase;

class TextDiffLineDiffTest extends TestCase
{
    public function test_normalize_for_line_diff_splits_adjacent_tags(): void
    {
        $raw = '<p>Hello</p><p>World</p>';
        $norm = TextDiff::normalizeForLineDiff($raw);
        $this->assertStringContainsString(">\n<", $norm);
        $this->assertStringNotContainsString('</p><p>', $norm);
    }

    public function test_lines_for_display_returns_colored_kinds_for_small_change_in_html(): void
    {
        $from = '<p>One fish</p><p>Two fish</p>';
        $to = '<p>One fish</p><p>Red fish</p>';
        $diff = TextDiff::linesForDisplay($from, $to);
        $this->assertNotNull($diff);
        $kinds = array_column($diff['lines'], 'kind');
        $this->assertContains('removed', $kinds);
        $this->assertContains('added', $kinds);
        $this->assertArrayHasKey('collapsed_same', $diff);
    }

    public function test_collapse_long_same_runs_inserts_warning(): void
    {
        $lines = [];
        for ($i = 0; $i < 40; $i++) {
            $lines[] = ['text' => 'same-line-'.$i, 'kind' => 'same'];
        }
        $collapsed = TextDiff::collapseLongSameRuns($lines, 18, 2);
        $this->assertLessThan(count($lines), count($collapsed));
        $this->assertContains('warning', array_column($collapsed, 'kind'));
    }
}
