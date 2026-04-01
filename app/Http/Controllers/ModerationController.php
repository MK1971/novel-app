<?php

namespace App\Http\Controllers;

use App\Models\ChapterStatistic;
use App\Models\Edit;
use App\Models\InlineEdit;
use App\Models\Payment;
use App\Support\AchievementUnlock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ModerationController extends Controller
{
    public function index()
    {
        Gate::authorize('admin');
        // `inline_edit` rows are payment stubs; real paragraph text lives in `inline_edits` (Inline Moderation).
        $edits = Edit::with(['user', 'chapter'])
            ->where('status', 'pending')
            ->where('type', '!=', 'inline_edit')
            ->orderBy('created_at')
            ->get();

        $pendingInlineEdits = InlineEdit::with(['user', 'chapter'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('admin.edits.index', compact('edits', 'pendingInlineEdits'));
    }

    public function approve(Edit $edit, Request $request)
    {
        Gate::authorize('admin');
        $validated = $request->validate([
            'status' => ['required', 'in:accepted_full,accepted_partial'],
            'merged_text' => ['nullable', 'string', 'max:100000'],
        ]);
        $status = $validated['status'];

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

        $update = [
            'status' => $status,
            'points_awarded' => $points,
        ];
        $merged = $validated['merged_text'] ?? null;
        if (is_string($merged) && trim($merged) !== '') {
            $update['edited_text'] = $merged;
        }

        $edit->update($update);

        if ($points > 0) {
            $edit->user->increment('points', $points);
        }

        AchievementUnlock::syncForUser($edit->user);

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

    public function approveInlineEdit(Request $request, InlineEdit $inlineEdit)
    {
        Gate::authorize('admin');
        $validated = $request->validate([
            'merged_text' => ['nullable', 'string', 'max:100000'],
        ]);
        $partial = $request->boolean('partial');
        $outcome = $partial ? InlineEdit::OUTCOME_PARTIAL : InlineEdit::OUTCOME_FULL;

        $update = [
            'status' => 'approved',
            'moderation_outcome' => $outcome,
        ];
        $merged = $validated['merged_text'] ?? null;
        if (is_string($merged) && trim($merged) !== '') {
            $update['suggested_text'] = $merged;
        }

        $inlineEdit->update($update);

        $hasPaid = $inlineEdit->payment_id
            && Payment::query()
                ->whereKey($inlineEdit->payment_id)
                ->where('status', 'completed')
                ->exists();

        if ($hasPaid) {
            $points = $partial ? 1 : 2;
            $inlineEdit->user->increment('points', $points);
        }

        AchievementUnlock::syncForUser($inlineEdit->user);

        $stats = ChapterStatistic::firstOrCreate(['chapter_id' => $inlineEdit->chapter_id]);
        $stats->increment('accepted_edits');

        if (! $hasPaid) {
            $message = 'Paragraph suggestion recorded as accepted, but no completed payment was on file — no points awarded.';
        } elseif ($partial) {
            $message = 'Paragraph suggestion accepted partial (1 pt).';
        } else {
            $message = 'Paragraph suggestion accepted full (2 pts).';
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
