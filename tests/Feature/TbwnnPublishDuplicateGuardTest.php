<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Chapter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TbwnnPublishDuplicateGuardTest extends TestCase
{
    use RefreshDatabase;

    public function test_publish_revision_rejects_body_identical_to_another_active_chapter(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $book = Book::create(['name' => Book::NAME_THE_BOOK_WITH_NO_NAME, 'status' => 'in_progress']);

        $ch1 = Chapter::create([
            'book_id' => $book->id,
            'title' => 'One',
            'number' => 1,
            'list_section' => Chapter::LIST_SECTION_CHAPTER,
            'content' => 'Original chapter one body.',
            'version' => 'A',
            'status' => 'published',
            'is_locked' => false,
            'is_archived' => false,
        ]);
        Chapter::create([
            'book_id' => $book->id,
            'title' => 'Two',
            'number' => 2,
            'list_section' => Chapter::LIST_SECTION_CHAPTER,
            'content' => 'Chapter two only.',
            'version' => 'A',
            'status' => 'published',
            'is_locked' => true,
            'locked_at' => now(),
            'is_archived' => false,
        ]);

        $this->actingAs($admin)
            ->from(route('admin.chapters.index'))
            ->post(route('admin.chapters.publish-story-revision'), [
                'chapter_id' => $ch1->id,
                'content' => 'Chapter two only.',
            ])
            ->assertSessionHasErrors('content');

        $ch1->refresh();
        $this->assertSame('Original chapter one body.', $ch1->content);
        $this->assertFalse((bool) $ch1->is_locked);
    }
}
