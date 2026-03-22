<?php

namespace App\Http\Controllers;

use App\Models\InlineEdit;
use App\Models\Edit;
use App\Models\ChapterStatistic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ModerationController extends Controller
{
    public function index()
    {
        Gate::authorize('admin');
        $edits = Edit::with(['user', 'chapter'])->where('status', 'pending')->orderBy('created_at')->get();
        return view('admin.edits.index', compact('edits'));
    }

    public function approve(Edit $edit, Request $request)
    {
        Gate::authorize('admin');
        $status = $request->input('status', 'accepted_full');
        $points = ($status === 'accepted_full') ? 2 : 1;
        
        $edit->update([
            'status' => $status,
            'points_awarded' => $points
        ]);
        
        $edit->user->increment('points', $points);
        
        $stats = ChapterStatistic::firstOrCreate(['chapter_id' => $edit->chapter_id]);
        $stats->increment('accepted_edits');
        
        return back()->with('success', 'Edit approved.');
    }

    public function reject(Edit $edit)
    {
        Gate::authorize('admin');
        $edit->update(['status' => 'rejected']);
        
        $stats = ChapterStatistic::firstOrCreate(['chapter_id' => $edit->chapter_id]);
        $stats->increment('rejected_edits');
        
        return back()->with('success', 'Edit rejected.');
    }

    public function inlineEdits()
    {
        Gate::authorize('admin');
        $inlineEdits = InlineEdit::with(['user', 'chapter'])->where('status', 'pending')->latest()->paginate(15);
        return view('admin.moderation.inline-edits', compact('inlineEdits'));
    }

    public function approveInlineEdit(InlineEdit $inlineEdit)
    {
        Gate::authorize('admin');
        $inlineEdit->update(['status' => 'approved']);
        
        // Award 1 point for inline edit
        $inlineEdit->user->increment('points', 1);
        
        $stats = ChapterStatistic::firstOrCreate(['chapter_id' => $inlineEdit->chapter_id]);
        $stats->increment('accepted_edits');
        
        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return back()->with('success', 'Inline edit approved.');
    }

    public function rejectInlineEdit(InlineEdit $inlineEdit)
    {
        Gate::authorize('admin');
        $inlineEdit->update(['status' => 'rejected']);
        
        $stats = ChapterStatistic::firstOrCreate(['chapter_id' => $inlineEdit->chapter_id]);
        $stats->increment('rejected_edits');
        
        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return back()->with('success', 'Inline edit rejected.');
    }
}
