<?php

namespace App\Http\Controllers;

use App\Models\ChapterStatistic;
use App\Models\Edit;
use App\Models\InlineEdit;
use App\Models\Payment;
use Illuminate\Http\Request;
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

        $hasCompletedPayment = Payment::query()
            ->where('edit_id', $edit->id)
            ->where('status', 'completed')
            ->exists();

        $points = 0;
        if ($hasCompletedPayment) {
            $points = match ($status) {
                'accepted_full' => 2,
                'accepted_partial' => 1,
                default => 0,
            };
        }

        $edit->update([
            'status' => $status,
            'points_awarded' => $points,
        ]);

        if ($points > 0) {
            $edit->user->increment('points', $points);
        }

        $stats = ChapterStatistic::firstOrCreate(['chapter_id' => $edit->chapter_id]);
        $stats->increment('accepted_edits');

        $message = 'Edit approved.';
        if (! $hasCompletedPayment) {
            $message .= ' No leaderboard points were awarded because there is no completed payment linked to this suggestion.';
        }

        return back()->with('success', $message);
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

        $hasPaid = $inlineEdit->payment_id
            && Payment::query()
                ->whereKey($inlineEdit->payment_id)
                ->where('status', 'completed')
                ->exists();

        if ($hasPaid) {
            $inlineEdit->user->increment('points', 1);
        }

        $stats = ChapterStatistic::firstOrCreate(['chapter_id' => $inlineEdit->chapter_id]);
        $stats->increment('accepted_edits');

        $message = 'Inline edit approved.';
        if (! $hasPaid) {
            $message .= ' No leaderboard points were awarded because this suggestion has no completed payment on file.';
        }

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', $message);
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
