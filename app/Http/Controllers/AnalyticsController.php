<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Edit;
use App\Models\InlineEdit;
use App\Models\Vote;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        $pendingChapterEdits = Edit::query()
            ->where('status', 'pending')
            ->where('type', '!=', 'inline_edit')
            ->count();

        $pendingInlineEdits = InlineEdit::query()
            ->where('status', 'pending')
            ->count();

        $insightSummary = [
            'total_votes' => Vote::query()->count(),
            'pending_edits' => $pendingChapterEdits + $pendingInlineEdits,
        ];

        // Get vote stats for Peter Trull chapters
        $voteStats = Vote::select('chapter_id', 'version_chosen', DB::raw('count(*) as total'))
            ->whereIn('chapter_id', function ($query) {
                $query->select('id')->from('chapters')->where('book_id', function ($q) {
                    $q->select('id')->from('books')->where('name', 'Peter Trull Solitary Detective');
                });
            })
            ->groupBy('chapter_id', 'version_chosen')
            ->get()
            ->map(function ($stat) {
                $chapter = Chapter::find($stat->chapter_id);
                $stat->chapter_number = $chapter?->number;
                $stat->chapter_title = $chapter?->title;
                $stat->chapter_group_key = $chapter ? $chapter->votePairGroupKey() : 'unknown';
                $stat->chapter_heading = $chapter ? $chapter->headingPrefix() : 'Chapter';

                return $stat;
            });

        $pendingChapterByChapterId = Edit::query()
            ->where('status', 'pending')
            ->where('type', '!=', 'inline_edit')
            ->selectRaw('chapter_id, count(*) as aggregate')
            ->groupBy('chapter_id')
            ->pluck('aggregate', 'chapter_id');

        $pendingInlineByChapterId = InlineEdit::query()
            ->where('status', 'pending')
            ->selectRaw('chapter_id, count(*) as aggregate')
            ->groupBy('chapter_id')
            ->pluck('aggregate', 'chapter_id');

        // Manuscript section: chapter_statistics (updates when suggestions are paid + moderated) + live pending queue
        $chapterStats = Chapter::with('statistics')
            ->where('book_id', function ($query) {
                $query->select('id')->from('books')->where('name', 'The Book With No Name');
            })
            ->orderByRaw(Chapter::listSectionOrderSql())
            ->orderBy('number')
            ->orderBy('id')
            ->get()
            ->map(function ($chapter) use ($pendingChapterByChapterId, $pendingInlineByChapterId) {
                $s = $chapter->statistics;
                $chapter->insight_submitted = (int) ($s->total_edits ?? 0);
                $chapter->insight_accepted = (int) ($s->accepted_edits ?? 0);
                $chapter->insight_rejected = (int) ($s->rejected_edits ?? 0);
                $chapter->insight_pending = (int) ($pendingChapterByChapterId[$chapter->id] ?? 0)
                    + (int) ($pendingInlineByChapterId[$chapter->id] ?? 0);

                return $chapter;
            });

        return view('analytics.index', compact(
            'voteStats',
            'chapterStats',
            'insightSummary',
        ));
    }
}
