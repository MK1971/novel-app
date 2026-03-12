<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Chapter;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $storyBook = Book::firstOrCreate(
            ['name' => 'The Book With No Name'],
            ['status' => 'in_progress']
        );

        if (!$storyBook->chapters()->exists()) {
            Chapter::create([
                'book_id' => $storyBook->id,
                'title' => 'Chapter 1',
                'number' => 1,
                'content' => 'This is the beginning of the story. Edit this chapter to improve it!',
                'version' => 'A',
                'status' => 'published',
            ]);
        }

        $peterTrull = Book::firstOrCreate(
            ['name' => 'Peter Trull Solitary Detective'],
            ['status' => 'finished']
        );

        if (!$peterTrull->chapters()->exists()) {
            Chapter::create([
                'book_id' => $peterTrull->id,
                'title' => 'Chapter 1',
                'number' => 1,
                'content' => 'Version A: The rain fell softly on the detective\'s coat as he stepped out of the shadows.',
                'version' => 'A',
                'status' => 'published',
            ]);
            Chapter::create([
                'book_id' => $peterTrull->id,
                'title' => 'Chapter 1',
                'number' => 1,
                'content' => 'Version B: A gentle rain pattered against Peter Trull\'s trench coat as he emerged from the alley.',
                'version' => 'B',
                'status' => 'published',
            ]);
        }
    }
}
