<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Chapter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class TbwnnChapterLockAndUploadDeadlineTest extends TestCase
{
    use RefreshDatabase;

    public function test_reader_index_auto_unlock_uses_latest_non_archived_chapter_only(): void
    {
        $book = Book::create(['name' => Book::NAME_THE_BOOK_WITH_NO_NAME, 'status' => 'in_progress']);
        $active = Chapter::create([
            'book_id' => $book->id,
            'title' => 'Current',
            'number' => 1,
            'list_section' => Chapter::LIST_SECTION_CHAPTER,
            'content' => 'Active body.',
            'version' => 'A',
            'status' => 'published',
            'is_locked' => false,
            'is_archived' => false,
        ]);
        $archived = Chapter::create([
            'book_id' => $book->id,
            'title' => 'Old slot',
            'number' => 2,
            'list_section' => Chapter::LIST_SECTION_CHAPTER,
            'content' => 'Archived body.',
            'version' => 'A',
            'status' => 'published',
            'is_locked' => false,
            'is_archived' => true,
            'is_reader_archive_link' => true,
        ]);

        $this->assertGreaterThan($active->id, $archived->id);

        $this->get(route('chapters.index'))->assertOk();

        $active->refresh();
        $archived->refresh();

        $this->assertFalse((bool) $active->is_locked, 'Current manuscript chapter must stay unlocked even if an archived row has a higher id.');
        $this->assertTrue((bool) $archived->is_locked);
    }

    public function test_tbwnn_upload_accepts_optional_paid_editing_close_date(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        Book::create(['name' => Book::NAME_THE_BOOK_WITH_NO_NAME, 'status' => 'in_progress']);

        $closeDay = now()->timezone(config('app.timezone'))->format('Y-m-d');

        $this->actingAs($admin)
            ->post(route('admin.chapters.store-story'), [
                'title' => 'One',
                'number' => 1,
                'list_section' => Chapter::LIST_SECTION_CHAPTER,
                'content' => 'Hello world.',
                'editing_closes_on' => $closeDay,
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $chapter = Chapter::query()->first();
        $this->assertNotNull($chapter);
        $expected = Carbon::createFromFormat('Y-m-d', $closeDay, config('app.timezone'))->endOfDay();
        $this->assertSame(
            $expected->timezone(config('app.timezone'))->format('Y-m-d H:i:s'),
            $chapter->editing_closes_at->timezone(config('app.timezone'))->format('Y-m-d H:i:s'),
            'editing_closes_at should be end-of-day for the chosen calendar date in app timezone.'
        );
    }

    public function test_tbwnn_reader_index_lists_version_a_only(): void
    {
        $book = Book::create(['name' => Book::NAME_THE_BOOK_WITH_NO_NAME, 'status' => 'in_progress']);
        Chapter::create([
            'book_id' => $book->id,
            'title' => 'Keep A',
            'number' => 1,
            'list_section' => Chapter::LIST_SECTION_CHAPTER,
            'content' => 'Alpha.',
            'version' => 'A',
            'status' => 'published',
            'is_locked' => false,
            'is_archived' => false,
        ]);
        Chapter::create([
            'book_id' => $book->id,
            'title' => 'Hide B',
            'number' => 1,
            'list_section' => Chapter::LIST_SECTION_CHAPTER,
            'content' => 'Beta.',
            'version' => 'B',
            'status' => 'published',
            'is_locked' => false,
            'is_archived' => false,
        ]);

        $this->get(route('chapters.index'))
            ->assertOk()
            ->assertSee('Keep A')
            ->assertDontSee('Hide B');
    }
}
