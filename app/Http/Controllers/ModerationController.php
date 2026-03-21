<?php

namespace App\Http\Controllers;

use App\Models\InlineEdit;
use App\Models\ChapterStatistic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModerationController extends Controller
{
    public function inlineEdits()
    {
        $edits = InlineEdit::with(['user', 'chapter'])->where('status', 'pending')->latest()->paginate(15);
        return view('admin.moderation.inline-edits', compact('edits'));
    }

    public function approveInlineEdit(InlineEdit $inlineEdit)
    {
        $inlineEdit->update(['status' => 'approved']);
        
        $stats = ChapterStatistic::firstOrCreate(['chapter_id' => $inlineEdit->chapter_id]);
        $stats->increment('accepted_edits');
        $stats->increment('total_edits');

        return back()->with('success', 'Inline edit approved.');
    }

    public function rejectInlineEdit(InlineEdit $inlineEdit)
    {
        $inlineEdit->update(['status' => 'rejected']);
        
        $stats = ChapterStatistic::firstOrCreate(['chapter_id' => $inlineEdit->chapter_id]);
        $stats->increment('rejected_edits');
        $stats->increment('total_edits');

        return back()->with('success', 'Inline edit rejected.');
    }
}
