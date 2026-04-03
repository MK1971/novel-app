<?php

namespace Tests\Unit;

use App\Models\ReadingProgress;
use PHPUnit\Framework\TestCase;

class ReadingProgressDisplayTest extends TestCase
{
    public function test_display_progress_matches_strict_when_extent_present(): void
    {
        $row = new ReadingProgress([
            'scroll_position' => 120,
            'scroll_extent_max' => 2400,
        ]);

        $this->assertSame(5, $row->scrollProgressPercent());
        $this->assertSame(5, $row->displayProgressPercent());
    }

    public function test_display_progress_estimates_when_extent_missing_but_position_positive(): void
    {
        $row = new ReadingProgress([
            'scroll_position' => 800,
            'scroll_extent_max' => 0,
        ]);

        $this->assertNull($row->scrollProgressPercent());
        $this->assertNotNull($row->displayProgressPercent());
        $this->assertGreaterThan(0, $row->displayProgressPercent());
        $this->assertLessThanOrEqual(100, $row->displayProgressPercent());
    }

    public function test_ratio_encoding_list_progress_maps_to_percent(): void
    {
        $row = new ReadingProgress([
            'scroll_position' => 725,
            'scroll_extent_max' => 1000,
        ]);

        $this->assertSame(73, $row->scrollProgressPercent());
        $this->assertSame(73, $row->displayProgressPercent());
    }
}
