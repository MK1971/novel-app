<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Chapter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChapterLogicalReaderPieceCountTest extends TestCase
{
    use RefreshDatabase;

    public function test_logical_count_uses_tbwnn_a_stream_and_one_slot_per_pt_pair(): void
    {
        $tbwnn = Book::create(['name' => Book::NAME_THE_BOOK_WITH_NO_NAME, 'status' => 'in_progress']);
        $pt = Book::create(['name' => Book::NAME_PETER_TRULL, 'status' => 'finished']);

        Chapter::create([
            'book_id' => $tbwnn->id,
            'title' => 'Cold open',
            'number' => 1,
            'list_section' => Chapter::LIST_SECTION_COLD_OPEN,
            'content' => 'cold',
            'version' => 'A',
            'status' => 'published',
        ]);
        Chapter::create([
            'book_id' => $tbwnn->id,
            'title' => 'Chapter 1',
            'number' => 1,
            'list_section' => Chapter::LIST_SECTION_CHAPTER,
            'content' => 'main a',
            'version' => 'A',
            'status' => 'published',
        ]);
        Chapter::create([
            'book_id' => $tbwnn->id,
            'title' => 'Chapter 1 B',
            'number' => 1,
            'list_section' => Chapter::LIST_SECTION_CHAPTER,
            'content' => 'main b',
            'version' => 'B',
            'status' => 'published',
        ]);

        Chapter::create([
            'book_id' => $pt->id,
            'title' => 'Round 1 A',
            'number' => 1,
            'list_section' => Chapter::LIST_SECTION_CHAPTER,
            'content' => 'a',
            'version' => 'A',
            'status' => 'published',
        ]);
        Chapter::create([
            'book_id' => $pt->id,
            'title' => 'Round 1 B',
            'number' => 1,
            'list_section' => Chapter::LIST_SECTION_CHAPTER,
            'content' => 'b',
            'version' => 'B',
            'status' => 'published',
        ]);

        $this->assertSame(3, Chapter::logicalReaderPieceCount());
        $this->assertSame(3, Chapter::logicalReaderPieceCount(publishedOnly: true));
    }

    public function test_tbwnn_main_stream_counts_lowercase_a_as_main_manuscript(): void
    {
        $tbwnn = Book::create(['name' => Book::NAME_THE_BOOK_WITH_NO_NAME, 'status' => 'in_progress']);
        Chapter::create([
            'book_id' => $tbwnn->id,
            'title' => 'Lower a',
            'number' => 1,
            'list_section' => Chapter::LIST_SECTION_CHAPTER,
            'content' => 'x',
            'version' => 'a',
            'status' => 'published',
        ]);

        $this->assertSame(1, Chapter::logicalReaderPieceCount());
    }

    public function test_published_only_excludes_draft(): void
    {
        $tbwnn = Book::create(['name' => Book::NAME_THE_BOOK_WITH_NO_NAME, 'status' => 'in_progress']);

        Chapter::create([
            'book_id' => $tbwnn->id,
            'title' => 'Live',
            'number' => 1,
            'list_section' => Chapter::LIST_SECTION_CHAPTER,
            'content' => 'x',
            'version' => 'A',
            'status' => 'published',
        ]);
        Chapter::create([
            'book_id' => $tbwnn->id,
            'title' => 'Draft',
            'number' => 2,
            'list_section' => Chapter::LIST_SECTION_CHAPTER,
            'content' => 'y',
            'version' => 'A',
            'status' => 'draft',
        ]);

        $this->assertSame(2, Chapter::logicalReaderPieceCount());
        $this->assertSame(1, Chapter::logicalReaderPieceCount(publishedOnly: true));
    }
}
