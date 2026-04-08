<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Chapter;
use App\Models\ReadingProgress;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_reading_progress_shows_locked_not_in_progress_for_locked_chapter(): void
    {
        $user = User::factory()->create();
        $book = Book::create([
            'name' => 'The Book With No Name',
            'status' => 'in_progress',
        ]);
        $chapter = Chapter::create([
            'book_id' => $book->id,
            'title' => 'Chapter One',
            'number' => 1,
            'content' => 'Body',
            'version' => 'A',
            'status' => 'published',
            'is_locked' => true,
            'is_archived' => false,
        ]);
        ReadingProgress::create([
            'user_id' => $user->id,
            'chapter_id' => $chapter->id,
            'scroll_position' => 100,
            'completed' => false,
            'last_read_at' => now(),
        ]);

        $html = $this->actingAs($user)
            ->get('/profile')
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('Locked', $html);
        $this->assertStringNotContainsString('>In Progress<', $html);
    }

    public function test_profile_page_renders_when_reading_progress_has_null_last_read_at(): void
    {
        $user = User::factory()->create();
        $book = Book::create([
            'name' => 'The Book With No Name',
            'status' => 'in_progress',
        ]);
        $chapter = Chapter::create([
            'book_id' => $book->id,
            'title' => 'Chapter One',
            'number' => 1,
            'content' => 'Body',
            'version' => 'A',
            'status' => 'published',
            'is_locked' => false,
            'is_archived' => false,
        ]);
        ReadingProgress::create([
            'user_id' => $user->id,
            'chapter_id' => $chapter->id,
            'scroll_position' => 0,
            'completed' => false,
            'last_read_at' => null,
        ]);

        $this->actingAs($user)
            ->get('/profile')
            ->assertOk()
            ->assertSee('Not recorded yet', false);
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('profile.edit'));

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_profile_avatar_upload_is_persisted_to_public_disk(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('avatar.jpg', 120, 120);

        $this->actingAs($user)
            ->patch('/profile', [
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $file,
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('profile.edit'));

        $user->refresh();
        $this->assertNotNull($user->avatar_path);
        Storage::disk('public')->assertExists($user->avatar_path);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('profile.edit'));

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }
}
