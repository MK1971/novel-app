<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Chapter;
use App\Models\ReadingProgress;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReaderTbwP1EnhancementsTest extends TestCase
{
    use RefreshDatabase;

    public function test_prizes_page_is_public(): void
    {
        $this->get(route('prizes'))
            ->assertOk()
            ->assertSee('Grand prize', false);
    }

    public function test_chapter_show_shows_adjacent_navigation_in_reader_order(): void
    {
        $book = Book::create(['name' => Book::NAME_THE_BOOK_WITH_NO_NAME, 'status' => 'in_progress']);
        $first = Chapter::create([
            'book_id' => $book->id,
            'title' => 'Alpha',
            'number' => 1,
            'list_section' => Chapter::LIST_SECTION_CHAPTER,
            'content' => str_repeat('word ', 50),
            'version' => 'A',
            'status' => 'published',
            'is_locked' => false,
            'is_archived' => false,
        ]);
        $second = Chapter::create([
            'book_id' => $book->id,
            'title' => 'Beta',
            'number' => 2,
            'list_section' => Chapter::LIST_SECTION_CHAPTER,
            'content' => str_repeat('word ', 50),
            'version' => 'A',
            'status' => 'published',
            'is_locked' => false,
            'is_archived' => false,
        ]);

        $this->get(route('chapters.show', $first))
            ->assertOk()
            ->assertSee('Next chapter', false)
            ->assertSee(route('chapters.show', $second), false);

        $this->get(route('chapters.show', $second))
            ->assertOk()
            ->assertSee('Previous chapter', false)
            ->assertSee(route('chapters.show', $first), false);
    }

    public function test_track_progress_stores_scroll_extent_max(): void
    {
        $book = Book::create(['name' => Book::NAME_THE_BOOK_WITH_NO_NAME, 'status' => 'in_progress']);
        $chapter = Chapter::create([
            'book_id' => $book->id,
            'title' => 'One',
            'number' => 1,
            'list_section' => Chapter::LIST_SECTION_CHAPTER,
            'content' => 'Body.',
            'version' => 'A',
            'status' => 'published',
            'is_locked' => false,
            'is_archived' => false,
        ]);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('chapters.track-progress', $chapter), [
                'scroll_position' => 120,
                'scroll_extent_max' => 2400,
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $row = ReadingProgress::query()
            ->where('user_id', $user->id)
            ->where('chapter_id', $chapter->id)
            ->first();
        $this->assertNotNull($row);
        $this->assertSame(120, (int) $row->scroll_position);
        $this->assertSame(2400, (int) $row->scroll_extent_max);
        $this->assertSame(5, $row->scrollProgressPercent());
    }

    public function test_track_progress_never_decreases_scroll_depth(): void
    {
        $book = Book::create(['name' => Book::NAME_THE_BOOK_WITH_NO_NAME, 'status' => 'in_progress']);
        $chapter = Chapter::create([
            'book_id' => $book->id,
            'title' => 'One',
            'number' => 1,
            'list_section' => Chapter::LIST_SECTION_CHAPTER,
            'content' => 'Body.',
            'version' => 'A',
            'status' => 'published',
            'is_locked' => false,
            'is_archived' => false,
        ]);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('chapters.track-progress', $chapter), [
                'scroll_position' => 2000,
                'scroll_extent_max' => 2000,
            ])
            ->assertOk();

        $this->actingAs($user)
            ->postJson(route('chapters.track-progress', $chapter), [
                'scroll_position' => 200,
                'scroll_extent_max' => 2000,
            ])
            ->assertOk();

        $row = ReadingProgress::query()
            ->where('user_id', $user->id)
            ->where('chapter_id', $chapter->id)
            ->first();
        $this->assertSame(2000, (int) $row->scroll_position);
        $this->assertSame(100, $row->scrollProgressPercent());
    }

    public function test_track_progress_stores_read_percent_as_ratio_encoding(): void
    {
        $book = Book::create(['name' => Book::NAME_THE_BOOK_WITH_NO_NAME, 'status' => 'in_progress']);
        $chapter = Chapter::create([
            'book_id' => $book->id,
            'title' => 'One',
            'number' => 1,
            'list_section' => Chapter::LIST_SECTION_CHAPTER,
            'content' => 'Body.',
            'version' => 'A',
            'status' => 'published',
            'is_locked' => false,
            'is_archived' => false,
        ]);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('chapters.track-progress', $chapter), [
                'read_percent' => 55,
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $row = ReadingProgress::query()
            ->where('user_id', $user->id)
            ->where('chapter_id', $chapter->id)
            ->first();
        $this->assertNotNull($row);
        $this->assertSame(1000, (int) $row->scroll_extent_max);
        $this->assertSame(550, (int) $row->scroll_position);
        $this->assertSame(55, $row->scrollProgressPercent());
    }

    public function test_vote_hub_shows_live_tbwnn_link_when_chapter_is_open(): void
    {
        Book::create(['name' => Book::NAME_PETER_TRULL, 'status' => 'in_progress']);
        $tbw = Book::create(['name' => Book::NAME_THE_BOOK_WITH_NO_NAME, 'status' => 'in_progress']);
        $open = Chapter::create([
            'book_id' => $tbw->id,
            'title' => 'Live',
            'number' => 1,
            'list_section' => Chapter::LIST_SECTION_CHAPTER,
            'content' => 'Open manuscript.',
            'version' => 'A',
            'status' => 'published',
            'is_locked' => false,
            'is_archived' => false,
        ]);

        $this->get(route('vote.index'))
            ->assertOk()
            ->assertSee('Open the live TBWNN chapter', false)
            ->assertSee(route('chapters.show', $open), false);
    }
}
