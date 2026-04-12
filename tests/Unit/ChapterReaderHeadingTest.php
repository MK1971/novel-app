<?php

namespace Tests\Unit;

use App\Models\Chapter;
use PHPUnit\Framework\TestCase;

class ChapterReaderHeadingTest extends TestCase
{
    public function test_display_title_without_custom_title_is_chapter_number_only(): void
    {
        $ch = new Chapter(['title' => '', 'number' => 9]);
        $ch->list_section = Chapter::LIST_SECTION_CHAPTER;

        $this->assertSame('9', $ch->displayTitle());
    }

    public function test_reader_heading_line_without_title_uses_prefix_only(): void
    {
        $ch = new Chapter(['title' => '', 'number' => 9]);
        $ch->list_section = Chapter::LIST_SECTION_CHAPTER;

        $this->assertSame('Chapter 9', $ch->readerHeadingLine());
    }

    public function test_reader_heading_line_with_title_includes_prefix(): void
    {
        $ch = new Chapter(['title' => 'The cellar', 'number' => 2]);
        $ch->list_section = Chapter::LIST_SECTION_CHAPTER;

        $this->assertSame('Chapter 2: The cellar', $ch->readerHeadingLine());
        $this->assertSame('The cellar', $ch->displayTitle());
    }

    public function test_reader_heading_line_for_cold_open_uses_title_without_prefix(): void
    {
        $ch = new Chapter(['title' => 'The cellar', 'number' => 0]);
        $ch->list_section = Chapter::LIST_SECTION_COLD_OPEN;

        $this->assertSame('The cellar', $ch->readerHeadingLine());
    }
}
