<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use Illuminate\Http\Response;

class RssFeedController extends Controller
{
    public function tbwChapters(): Response
    {
        $chapters = Chapter::query()
            ->forTbwReaderManuscript()
            ->with('book')
            ->orderByRaw(Chapter::listSectionOrderSql())
            ->orderBy('number')
            ->orderBy('id')
            ->get();

        return response()
            ->view('feeds.tbw-chapters', compact('chapters'))
            ->header('Content-Type', 'application/rss+xml; charset=UTF-8');
    }
}
