<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ChapterController extends Controller
{
    public function index()
    {
        Gate::authorize('admin');
        $storyBook = Book::where('name', 'The Book With No Name')->first();
        $peterTrull = Book::where('name', 'Peter Trull Solitary Detective')->first();

        $storyChapters = $storyBook?->chapters()->where('version', 'A')
            ->orderByRaw(Chapter::listSectionOrderSql())
            ->orderBy('number')
            ->orderBy('id')
            ->get() ?? collect();
        $peterChapters = $peterTrull?->chapters()
            ->orderByRaw(Chapter::listSectionOrderSql())
            ->orderBy('number')
            ->orderBy('version')
            ->get() ?? collect();

        return view('admin.chapters.index', compact('storyBook', 'peterTrull', 'storyChapters', 'peterChapters'));
    }

    public function storeStoryChapter(Request $request)
    {
        Gate::authorize('admin');
        $request->validate([
            'title' => 'required|string|max:255',
            'number' => 'required|integer|min:0',
            'list_section' => ['required', Rule::in(Chapter::LIST_SECTIONS)],
            'content' => 'required|string',
        ]);

        $book = Book::firstOrCreate(
            ['name' => 'The Book With No Name'],
            ['status' => 'in_progress']
        );

        // Archive all previous chapters of this book with the same chapter number
        Chapter::where('book_id', $book->id)
            ->where('number', $request->number)
            ->update(['is_archived' => true]);

        // Lock all previous chapters of this book
        Chapter::where('book_id', $book->id)->update(['is_locked' => true]);

        Chapter::create([
            'book_id' => $book->id,
            'title' => $request->title,
            'number' => $request->number,
            'list_section' => $request->list_section,
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

        // Archive all previous chapters of this book with the same chapter number
        Chapter::where('book_id', $book->id)
            ->where('number', $request->number)
            ->update(['is_archived' => true]);

        // Lock all previous chapters of this book
        Chapter::where('book_id', $book->id)->update(['is_locked' => true]);

        Chapter::create([
            'book_id' => $book->id,
            'title' => $request->title,
            'number' => $request->number,
            'list_section' => $request->list_section,
            'content' => $request->content_a,
            'version' => 'A',
            'status' => 'published',
            'is_locked' => false,
        ]);
        Chapter::create([
            'book_id' => $book->id,
            'title' => $request->title,
            'number' => $request->number,
            'list_section' => $request->list_section,
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
        $chapter->is_locked = ! $chapter->is_locked;
        $chapter->save();

        return back()->with('success', 'Chapter lock status updated.');
    }

    public function archive(Chapter $chapter)
    {
        Gate::authorize('admin');
        $chapter->is_archived = true;
        $chapter->save();

        return back()->with('success', 'Chapter archived successfully.');
    }
}
