<?php

namespace Tests\Unit;

use App\Support\TbwRevisionMergePreview;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class TbwRevisionMergePreviewTest extends TestCase
{
    public function test_markers_to_html_wraps_accepted_regions_in_mark_and_strips_control_delimiters(): void
    {
        $method = new ReflectionMethod(TbwRevisionMergePreview::class, 'markersToHtml');
        $method->setAccessible(true);

        $marked = "\x1ESi4\x1F".'For a moment, there was only the sound.'."\x1EEi4\x1F";

        $html = $method->invoke(null, $marked);

        $this->assertStringContainsString('<mark class="bg-emerald-200/90', $html);
        $this->assertStringContainsString('For a moment, there was only the sound.', $html);
        $this->assertStringNotContainsString('Si4', $html);
        $this->assertStringNotContainsString('Ei4', $html);
    }
}
