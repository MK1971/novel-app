<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Chapter;
use App\Support\ChapterLifecycle;
use App\Support\TbwRevisionMergePreview;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ChapterController extends Controller
{
    /** Session key: persisted TBWNN "new chapter" form when upload is blocked (so leaving the page does not lose work). */
    public const SESSION_TBWNN_UPLOAD_DRAFT = 'admin_tbwnn_chapter_draft';

    public const SESSION_PT_UPLOAD_DRAFT = 'admin_peter_trull_upload_draft';

    public function index()
    {
        Gate::authorize('admin');
        $storyBook = Book::where('name', Book::NAME_THE_BOOK_WITH_NO_NAME)->first();
        $peterTrull = Book::where('name', Book::NAME_PETER_TRULL)->first();

        $storyChapters = $storyBook?->chapters()
            ->where('version', 'A')
            ->where('is_archived', false)
            ->orderByRaw(Chapter::listSectionOrderSql())
            ->orderBy('number')
            ->orderBy('id')
            ->get() ?? collect();

        $peterChapters = $peterTrull?->chapters()
            ->where('is_archived', false)
            ->orderByRaw(Chapter::listSectionOrderSql())
            ->orderBy('number')
            ->orderBy('version')
            ->get() ?? collect();

        $revisionPreviews = [];
        foreach ($storyChapters->where('is_locked', false) as $ch) {
            $hasAccepted = ChapterLifecycle::hasAcceptedSuggestionsForChapter($ch);
            $revisionPreviews[$ch->id] = [
                'has_accepted' => $hasAccepted,
                'merged_plain' => $hasAccepted ? TbwRevisionMergePreview::mergedPlainText($ch) : null,
                'merged_html' => $hasAccepted ? TbwRevisionMergePreview::mergedHighlightedHtml($ch) : null,
            ];
        }

        $tbwnnUploadDraft = session(self::SESSION_TBWNN_UPLOAD_DRAFT, []);
        $peterTrullUploadDraft = session(self::SESSION_PT_UPLOAD_DRAFT, []);

        return view('admin.chapters.index', compact('storyBook', 'peterTrull', 'storyChapters', 'peterChapters', 'revisionPreviews', 'tbwnnUploadDraft', 'peterTrullUploadDraft'));
    }

    public function storeStoryChapter(Request $request)
    {
        Gate::authorize('admin');
        $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'number' => 'required|integer|min:0',
            'list_section' => ['required', Rule::in(Chapter::LIST_SECTIONS)],
            'content' => 'required|string',
            'editing_closes_on' => ['nullable', 'date', 'date_format:Y-m-d'],
        ]);

        $gate = ChapterLifecycle::canUploadNextStoryChapter();
        if ($gate !== true) {
            $request->session()->put(self::SESSION_TBWNN_UPLOAD_DRAFT, [
                'title' => (string) $request->input('title', ''),
                'number' => $request->input('number'),
                'list_section' => (string) $request->input('list_section', 'chapter'),
                'content' => (string) $request->input('content', ''),
                'editing_closes_on' => (string) $request->input('editing_closes_on', ''),
            ]);

            return redirect()
                ->route('admin.chapters.index')
                ->withFragment('tbwnn-new-chapter-upload')
                ->with('error', $gate)
                ->withInput();
        }

        $request->session()->forget(self::SESSION_TBWNN_UPLOAD_DRAFT);

        $book = Book::firstOrCreate(
            ['name' => Book::NAME_THE_BOOK_WITH_NO_NAME],
            ['status' => 'in_progress']
        );

        ChapterLifecycle::archiveTbwRowsForSlot($book, (int) $request->number, $request->list_section);

        Chapter::where('book_id', $book->id)
            ->where('is_locked', false)
            ->update(['is_locked' => true, 'locked_at' => now()]);

        $now = now();
        $tz = config('app.timezone');
        $closesAt = $now->copy()->addDays(30);
        if ($request->filled('editing_closes_on')) {
            $day = (string) $request->input('editing_closes_on');
            $closesAt = Carbon::createFromFormat('Y-m-d', $day, $tz)->endOfDay();
            $pubDay = $now->copy()->timezone($tz)->format('Y-m-d');
            if ($day < $pubDay) {
                return back()
                    ->withErrors(['editing_closes_on' => 'Close date must be on or after the chapter publication day (today).'])
                    ->withInput();
            }
        }

        Chapter::create([
            'book_id' => $book->id,
            'title' => trim((string) ($request->input('title') ?? '')),
            'number' => $request->number,
            'list_section' => $request->list_section,
            'content' => $request->content,
            'version' => 'A',
            'status' => 'published',
            'is_locked' => false,
            'published_at' => $now,
            'editing_closes_at' => $closesAt,
        ]);

        return back()->with('success', 'Chapter added and previous chapters locked.');
    }

    public function publishStoryRevision(Request $request)
    {
        Gate::authorize('admin');
        $request->validate([
            'chapter_id' => 'required|exists:chapters,id',
            'content' => 'required|string',
        ]);

        $chapter = Chapter::with('book')->findOrFail($request->chapter_id);

        if (! ChapterLifecycle::isTbwChapter($chapter) || $chapter->is_archived) {
            return back()->with('error', 'Only open manuscript chapters can receive a published revision.');
        }

        if ($chapter->is_locked) {
            return back()->with('error', 'That chapter is already locked.');
        }

        $incoming = $request->string('content')->value();
        $duplicateOtherId = Chapter::query()
            ->where('book_id', $chapter->book_id)
            ->where('id', '!=', $chapter->id)
            ->where('is_archived', false)
            ->where('content', $incoming)
            ->value('id');
        if ($duplicateOtherId !== null) {
            return back()
                ->withErrors([
                    'content' => 'This text is identical to another active manuscript chapter (database id '.$duplicateOtherId.'). '
                        .'Publish & lock only replaces the one chapter you picked — you may be pasting a different chapter’s file into the wrong box. '
                        .'Use “Upload & Lock Previous” at the top to add the next chapter.',
                ])
                ->withInput();
        }

        if (ChapterLifecycle::hasAcceptedSuggestionsForChapter($chapter)) {
            $chapter->update([
                'content' => $request->content,
                'is_locked' => true,
                'locked_at' => now(),
            ]);

            return back()->with('success', 'Integrated text published and chapter locked.');
        }

        if (ChapterLifecycle::hasPendingSuggestionsForChapter($chapter)) {
            return back()->with(
                'error',
                'There are still pending suggestions on this chapter. Moderate them first, or publish a merge when you have accepted edits.'
            );
        }

        $chapter->update([
            'content' => $request->content,
            'is_locked' => true,
            'locked_at' => now(),
        ]);

        return back()->with('success', 'Final version uploaded and chapter locked.');
    }

    public function closeStoryWithoutMergedUpload(Request $request, Chapter $chapter)
    {
        Gate::authorize('admin');
        $chapter->loadMissing('book');

        if (! ChapterLifecycle::canCloseTbwWithoutMergedUpload($chapter)) {
            return back()->with(
                'error',
                'This chapter is already locked or is not part of The Book With No Name.'
            );
        }

        $chapter->update(['is_locked' => true, 'locked_at' => now()]);

        return back()->with('success', 'Chapter closed without a merged upload. You can upload the next chapter when ready.');
    }

    public function extendEditingWindow(Request $request, Chapter $chapter)
    {
        Gate::authorize('admin');
        $chapter->loadMissing('book');

        if ($chapter->is_archived) {
            return back()->with('error', 'Invalid chapter.');
        }

        $validated = $request->validate([
            'editing_closes_on' => ['required', 'date', 'date_format:Y-m-d'],
        ]);

        $tz = config('app.timezone');
        $closesAt = Carbon::createFromFormat('Y-m-d', $validated['editing_closes_on'], $tz)->endOfDay();

        $published = $chapter->published_at ?? $chapter->created_at;
        if ($published) {
            $pubDay = $published->copy()->timezone($tz)->format('Y-m-d');
            if ($validated['editing_closes_on'] < $pubDay) {
                return back()
                    ->withErrors(['editing_closes_on' => 'Close date must be on or after the chapter publication day.'])
                    ->withInput();
            }
        }

        if (ChapterLifecycle::isPeterTrullChapter($chapter)) {
            $section = $chapter->list_section ?: Chapter::LIST_SECTION_CHAPTER;
            Chapter::query()
                ->where('book_id', $chapter->book_id)
                ->where('number', $chapter->number)
                ->where('is_archived', false)
                ->where(function ($q) use ($section) {
                    $q->where('list_section', $section);
                    if ($section === Chapter::LIST_SECTION_CHAPTER) {
                        $q->orWhereNull('list_section');
                    }
                })
                ->update([
                    'editing_closes_at' => $closesAt,
                    'editing_deadline_reminder_sent_at' => null,
                ]);

            return back()->with('success', 'Voting close date updated for this pair.');
        }

        if (! ChapterLifecycle::isTbwChapter($chapter)) {
            return back()->with('error', 'Invalid chapter.');
        }

        $chapter->editing_closes_at = $closesAt;
        $chapter->editing_deadline_reminder_sent_at = null;
        $chapter->save();

        return back()->with('success', 'Paid editing close date updated.');
    }

    public function storePeterTrullChapter(Request $request)
    {
        Gate::authorize('admin');

        $request->session()->put(self::SESSION_PT_UPLOAD_DRAFT, [
            'title' => (string) $request->input('title', ''),
            'number' => $request->input('number'),
            'list_section' => (string) $request->input('list_section', 'chapter'),
            'content_a' => (string) $request->input('content_a', ''),
            'content_b' => (string) $request->input('content_b', ''),
            'archive_winning_version' => (string) $request->input('archive_winning_version', 'auto'),
        ]);

        try {
            $validated = $request->validate([
                'title' => ['nullable', 'string', 'max:255'],
                'number' => 'required|integer|min:0',
                'list_section' => ['required', Rule::in(Chapter::LIST_SECTIONS)],
                'content_a' => 'required|string',
                'content_b' => 'required|string',
                'archive_winning_version' => ['nullable', Rule::in(['auto', 'A', 'B'])],
            ]);
        } catch (ValidationException $e) {
            return redirect()
                ->route('admin.chapters.index')
                ->withFragment('peter-trull-upload')
                ->withErrors($e->errors())
                ->withInput();
        }

        $title = trim((string) ($validated['title'] ?? ''));

        Log::info('admin.peter_trull.upload.hit', [
            'user_id' => auth()->id(),
            'title' => $title,
            'number' => $validated['number'],
            'list_section' => $validated['list_section'],
        ]);

        try {
            $book = Book::firstOrCreate(
                ['name' => Book::NAME_PETER_TRULL],
                ['status' => 'finished']
            );

            $number = (int) $validated['number'];
            $listSection = $validated['list_section'];

            $activePair = Chapter::query()
                ->where('book_id', $book->id)
                ->where('number', $number)
                ->where('list_section', $listSection)
                ->where('is_archived', false)
                ->orderBy('version')
                ->get();

            if ($activePair->count() >= 2) {
                $mode = $validated['archive_winning_version'] ?? 'auto';
                $winner = ChapterLifecycle::resolvePeterTrullWinningVersion($activePair, $mode === 'auto' ? 'auto' : $mode);

                foreach ($activePair as $row) {
                    $row->update([
                        'is_archived' => true,
                        'is_reader_archive_link' => false,
                    ]);
                }

                $winnerLabel = trim((string) ($winner->title ?? '')) !== '' ? $winner->title : (string) $winner->number;

                Chapter::create([
                    'book_id' => $book->id,
                    'title' => $winnerLabel.' — archived (version '.$winner->version.' won)',
                    'number' => $number,
                    'list_section' => $listSection,
                    'content' => $winner->content,
                    'version' => 'A',
                    'status' => 'published',
                    'is_locked' => true,
                    'locked_at' => now(),
                    'is_archived' => true,
                    'is_reader_archive_link' => true,
                ]);
            } else {
                $ids = Chapter::query()
                    ->where('book_id', $book->id)
                    ->where('number', $number)
                    ->where('list_section', $listSection)
                    ->pluck('id');

                if ($ids->isNotEmpty()) {
                    $canonicalId = $ids->max();
                    Chapter::query()->whereIn('id', $ids)->update([
                        'is_archived' => true,
                        'is_reader_archive_link' => false,
                    ]);
                    Chapter::query()->where('id', $canonicalId)->update(['is_reader_archive_link' => true]);
                }
            }

            Chapter::where('book_id', $book->id)
                ->where('is_locked', false)
                ->update(['is_locked' => true, 'locked_at' => now()]);

            $now = now();
            $closesAt = $now->copy()->addDays(30);

            $chA = Chapter::create([
                'book_id' => $book->id,
                'title' => $title,
                'number' => $number,
                'list_section' => $listSection,
                'content' => $validated['content_a'],
                'version' => 'A',
                'status' => 'published',
                'is_locked' => false,
                'published_at' => $now,
                'editing_closes_at' => $closesAt,
            ]);
            $chB = Chapter::create([
                'book_id' => $book->id,
                'title' => $title,
                'number' => $number,
                'list_section' => $listSection,
                'content' => $validated['content_b'],
                'version' => 'B',
                'status' => 'published',
                'is_locked' => false,
                'published_at' => $now,
                'editing_closes_at' => $closesAt,
            ]);

            Log::info('admin.peter_trull.upload.success', [
                'book_id' => $book->id,
                'chapter_a_id' => $chA->id,
                'chapter_b_id' => $chB->id,
            ]);
        } catch (\Throwable $e) {
            Log::error('admin.peter_trull.upload.failed', [
                'message' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return redirect()
                ->route('admin.chapters.index')
                ->withFragment('peter-trull-upload')
                ->with('error', 'Could not save this Peter Trull pair: '.$e->getMessage());
        }

        $request->session()->forget(self::SESSION_PT_UPLOAD_DRAFT);

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
        $chapter->locked_at = $chapter->is_locked ? now() : null;
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
