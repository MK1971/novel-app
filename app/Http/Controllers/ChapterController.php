<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Chapter;
use Illuminate\Http\Request;

class ChapterController extends Controller
{
    public function index()
    {
        $book = Book::where('name', 'The Book With No Name')->first();
        $chapters = $book?->chapters()->where('version', 'A')->orderBy('number')->get() ?? collect();
        return view('chapters.index', compact('chapters', 'book'));
    }

    public function show(Chapter $chapter)
    {
        return view('chapters.show', compact('chapter'));
    }
}
