<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ChapterController extends Controller
{
    public function index()
    {
        Gate::authorize('admin');
        $storyBook = Book::where('name', 'The Book With No Name')->first();
        $peterTrull = Book::where('name', 'Peter Trull Solitary Detective')->first();

        $storyChapters = $storyBook?->chapters()->where('version', 'A')->orderBy('number')->get() ?? collect();
        $peterChapters = $peterTrull?->chapters()->orderBy('number')->orderBy('version')->get() ?? collect();

        return view('admin.chapters.index', compact('storyBook', 'peterTrull', 'storyChapters', 'peterChapters'));
    }

    public function storeStoryChapter(Request $request)
    {
        Gate::authorize('admin');
        $request->validate([
            'title' => 'required|string|max:255',
            'number' => 'required|integer|min:1',
            'content' => 'required|string',
        ]);

        $book = Book::firstOrCreate(
            ['name' => 'The Book With No Name'],
            ['status' => 'in_progress']
        );

        // Lock all previous chapters of this book
        Chapter::where('book_id', $book->id)->update(['is_locked' => true]);

        Chapter::create([
            'book_id' => $book->id,
            'title' => $request->title,
            'number' => $request->number,
            'content' => $request->content,
            'version' => 'A',
            'status' => 'published',
            'is_locked' => false,
        ]);

        return back()->with('success', 'Chapter added and previous chapters locked.');
    }

    public function storePeterTrullChapter(Request $request)
    {
        Gate::authorize('admin');
        $request->validate([
            'title' => 'required|string|max:255',
            'number' => 'required|integer|min:1',
            'content_a' => 'required|string',
            'content_b' => 'required|string',
        ]);

        $book = Book::firstOrCreate(
            ['name' => 'Peter Trull Solitary Detective'],
            ['status' => 'finished']
        );

        // Lock all previous chapters of this book
        Chapter::where('book_id', $book->id)->update(['is_locked' => true]);

        Chapter::create([
            'book_id' => $book->id,
            'title' => $request->title,
            'number' => $request->number,
            'content' => $request->content_a,
            'version' => 'A',
            'status' => 'published',
            'is_locked' => false,
        ]);
        Chapter::create([
            'book_id' => $book->id,
            'title' => $request->title,
            'number' => $request->number,
            'content' => $request->content_b,
            'version' => 'B',
            'status' => 'published',
            'is_locked' => false,
        ]);

        return back()->with('success', 'Chapter pair added and previous chapters locked.');
    }

    public function destroy(Chapter $chapter)
    {
        Gate::authorize('admin');
        $chapter->delete();
        return back()->with('success', 'Chapter deleted successfully.');
    }

    public function toggleLock(Chapter $chapter)
    {
        Gate::authorize('admin');
        $chapter->is_locked = !$chapter->is_locked;
        $chapter->save();
        return back()->with('success', 'Chapter lock status updated.');
    }
}
