<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <nav class="mb-2 text-xs font-bold text-amber-800/70">
                    <a href="{{ route('dashboard') }}" class="underline">Dashboard</a> / Admin / Chapters
                </nav>
                <h2 class="font-extrabold text-3xl text-amber-900">
                    📚 Manage Chapters
                </h2>
            </div>
            <p class="text-amber-800/60 font-bold">Upload, lock, and delete chapters</p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            @if (session('error'))
                <div class="p-4 bg-red-100 text-red-900 rounded-2xl border-2 border-red-300 shadow-sm font-bold space-y-2" role="alert">
                    <p class="text-lg font-extrabold">Upload blocked</p>
                    <p class="text-sm leading-relaxed">{{ session('error') }}</p>
                    <p class="text-xs font-bold text-red-800/80">What you typed is kept in the form below (and saved until an upload succeeds or you replace it).</p>
                </div>
            @endif

            @if (session('success'))
                <div class="p-4 bg-green-100 text-green-700 rounded-2xl border border-green-200 shadow-sm font-bold">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 bg-red-100 text-red-800 rounded-2xl border border-red-200 shadow-sm font-bold space-y-1" role="alert">
                    <p class="font-extrabold">Could not save — please fix the following:</p>
                    <ul class="list-disc list-inside text-sm font-bold">
                        @foreach ($errors->all() as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Upload chapter for The Book With No Name --}}
            <div class="bg-white border-2 border-amber-100 rounded-[2rem] p-8 shadow-sm">
                <h3 class="text-2xl font-extrabold text-amber-900 mb-2">📖 The Book With No Name</h3>
                <p class="text-amber-800/60 font-bold mb-4">Add a new chapter. Previous chapters will be automatically locked for editing.</p>
                <p class="text-sm font-bold text-amber-800/70 mb-8 leading-relaxed">
                    For each <strong>open</strong> chapter: if you have <strong>accepted</strong> suggestions, use <strong>Publish integrated revision</strong> — the preview highlights accepted contributions in green; edit the merged text in the box if needed, then lock. If you want to move on without merging suggestion text, use <strong>Close without merged text</strong> to force-lock the chapter and upload the next one.
                </p>

                @php
                    $tbDraft = is_array($tbwnnUploadDraft ?? null) ? $tbwnnUploadDraft : [];
                    $tbDraftActive = filled($tbDraft['title'] ?? null) || filled($tbDraft['content'] ?? null) || array_key_exists('number', $tbDraft);
                @endphp
                @if($tbDraftActive && ! session('error'))
                    <div class="mb-6 p-4 bg-amber-100/80 border border-amber-300 rounded-xl text-sm font-bold text-amber-900">
                        A previous <strong>new chapter</strong> draft is restored below (saved after an upload was blocked). It clears automatically when a chapter uploads successfully.
                    </div>
                @endif
                
                <form id="tbwnn-new-chapter-upload" action="{{ route('admin.chapters.store-story') }}" method="POST" class="space-y-6 mb-12">
                    @csrf
                    <div class="grid gap-6 md:grid-cols-3">
                        <div>
                            <label class="block text-amber-900 font-extrabold mb-2">Title <span class="text-amber-600 font-bold normal-case">(optional)</span></label>
                            <input type="text" name="title" value="{{ old('title', $tbDraft['title'] ?? '') }}" class="w-full bg-amber-50 border-2 border-amber-100 rounded-xl px-4 py-3 text-amber-900 focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none transition-all font-bold" placeholder="Optional — blank shows chapter number only (e.g. Chapter 3)">
                        </div>
                        <div>
                            <label class="block text-amber-900 font-extrabold mb-2">List section</label>
                            <select name="list_section" class="w-full bg-amber-50 border-2 border-amber-100 rounded-xl px-4 py-3 text-amber-900 focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none transition-all font-bold" required>
                                @php $ls = old('list_section', $tbDraft['list_section'] ?? 'chapter'); @endphp
                                <option value="cold_open" @selected($ls === 'cold_open')>Cold open</option>
                                <option value="prolog" @selected($ls === 'prolog')>Prolog</option>
                                <option value="chapter" @selected($ls === 'chapter')>Chapter</option>
                                <option value="epilog" @selected($ls === 'epilog')>Epilog</option>
                            </select>
                            <p class="text-xs font-bold text-amber-800/50 mt-2">Order on the reader list: cold open → prolog → chapters → epilog.</p>
                        </div>
                        <div>
                            <label class="block text-amber-900 font-extrabold mb-2">Sort number</label>
                            <input type="number" name="number" value="{{ old('number', array_key_exists('number', $tbDraft) ? $tbDraft['number'] : $storyChapters->count() + 1) }}" min="0" class="w-full bg-amber-50 border-2 border-amber-100 rounded-xl px-4 py-3 text-amber-900 focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none transition-all font-bold" required>
                            <p class="text-xs font-bold text-amber-800/50 mt-2">Within the same section, lower numbers appear first. Use 0 for cold open / prolog / epilog if you only have one of each.</p>
                        </div>
                    </div>
                    <div class="max-w-md">
                        <label class="block text-amber-900 font-extrabold mb-2">Paid editing closes <span class="text-amber-600 font-bold normal-case">(optional)</span></label>
                        <input
                            type="date"
                            name="editing_closes_on"
                            value="{{ old('editing_closes_on', $tbDraft['editing_closes_on'] ?? '') }}"
                            class="w-full bg-amber-50 border-2 border-amber-100 rounded-xl px-4 py-3 text-amber-900 focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none transition-all font-bold"
                        >
                        <p class="text-xs font-bold text-amber-800/50 mt-2">Calendar day in {{ config('app.timezone') }}, same as &ldquo;Save close date&rdquo; on an open chapter. Leave blank to use <strong class="text-amber-800">30 days from upload</strong> (a new upload always got a fresh 30-day window and did not inherit the date you set on the previous chapter).</p>
                        @error('editing_closes_on')
                            <p class="text-xs font-bold text-red-600 mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-amber-900 font-extrabold mb-2">Content</label>
                        <textarea name="content" rows="6" class="w-full bg-amber-50 border-2 border-amber-100 rounded-xl px-4 py-3 text-amber-900 focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none transition-all font-bold" required>{{ old('content', $tbDraft['content'] ?? '') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-amber-900 font-extrabold mb-2">Reader / Peter Trull note <span class="text-amber-600 font-bold normal-case">(optional)</span></label>
                        <textarea name="reader_blurb" rows="3" class="w-full bg-amber-50 border-2 border-amber-100 rounded-xl px-4 py-3 text-amber-900 focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none transition-all font-bold" placeholder="Short intro for readers — e.g. what this upload is about, or Peter Trull’s angle.">{{ old('reader_blurb', $tbDraft['reader_blurb'] ?? '') }}</textarea>
                        <p class="text-xs font-bold text-amber-800/50 mt-2">Shown above the chapter body on the public chapter page when filled.</p>
                    </div>
                    <div class="flex flex-col gap-2 max-w-xl">
                        <label class="inline-flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="is_pilot" value="1" class="mt-1 rounded border-amber-300 text-amber-600 focus:ring-amber-500" @checked(old('is_pilot', $tbDraft['is_pilot'] ?? false))>
                            <span>
                                <span class="block text-amber-900 font-extrabold">Pilot chapter (first upload)</span>
                                <span class="block text-xs font-bold text-amber-800/70 mt-1">Skips the default 30-day calendar close. Paid editing stops automatically after <strong>{{ config('tbwnn.pilot.close_after_accepted_edits', 50) }}</strong> accepted suggestions (story + inline). Use for the opening round only; later chapters use the normal window.</span>
                            </span>
                        </label>
                    </div>
                    <button type="submit" class="px-8 py-3 bg-amber-600 text-white font-extrabold rounded-xl hover:bg-amber-700 transition-all shadow-lg shadow-amber-600/20">
                        Upload & Lock Previous
                    </button>
                </form>

                @if($storyChapters->where('is_locked', false)->isNotEmpty())
                    <div class="mb-12 p-6 bg-amber-50/80 border-2 border-amber-200 rounded-2xl space-y-8">
                        <h4 class="text-lg font-extrabold text-amber-900">Open chapter workflow</h4>
                        @foreach($storyChapters->where('is_locked', false) as $openCh)
                            <div class="p-6 bg-white border border-amber-100 rounded-xl space-y-4">
                                <div>
                                    <p class="font-extrabold text-amber-900">{{ $openCh->readerHeadingLine() }}</p>
                                    <p class="text-xs font-bold text-amber-700/70">Sort #{{ $openCh->number }} · id {{ $openCh->id }}</p>
                                    @if($openCh->editing_closes_at)
                                        <p class="text-xs font-bold text-amber-800 mt-1">Editing window closes: {{ $openCh->editing_closes_at->format('M j, Y g:i A') }}</p>
                                    @endif
                                </div>
                                @php
                                    $rev = $revisionPreviews[$openCh->id] ?? ['has_accepted' => false, 'merged_plain' => null, 'merged_html' => null];
                                    $defaultPublishBody = $rev['has_accepted'] && $rev['merged_plain'] !== null
                                        ? $rev['merged_plain']
                                        : $openCh->content;
                                @endphp
                                <div class="rounded-xl border-2 border-amber-300/80 bg-amber-50 px-4 py-3 text-xs font-bold text-amber-900 leading-relaxed">
                                    <strong class="font-extrabold">This form replaces text for this row only</strong> (sort #{{ $openCh->number }}, id {{ $openCh->id }} — {{ $openCh->readerHeadingLine() }}). It does <strong>not</strong> create the next chapter.
                                    Adding the next chapter is always the <strong>Upload & Lock Previous</strong> section above. Pasting the wrong file here can overwrite an earlier chapter’s body in the database.
                                </div>
                                @php $confirmPublishMsg = 'Replace stored text for sort #'.$openCh->number.' (id '.$openCh->id.') and lock this chapter? This does not add a new chapter row.'; @endphp
                                <form action="{{ route('admin.chapters.publish-story-revision') }}" method="POST" class="space-y-3 border-t border-amber-100 pt-4" onsubmit="return confirm(@json($confirmPublishMsg));">
                                    @csrf
                                    <input type="hidden" name="chapter_id" value="{{ $openCh->id }}">
                                    @if($rev['has_accepted'] && $rev['merged_html'])
                                        <label class="block text-sm font-extrabold text-amber-900">Preview — accepted contributions (highlighted)</label>
                                        <p class="text-xs font-bold text-amber-700/80 mb-2">Green shows the <strong>merge text stored at approval</strong> (what you set on Review Suggestions, including any edits you made there). Tweak the full chapter in the box below if you still need changes, then publish.</p>
                                        <div class="max-h-64 overflow-y-auto rounded-xl border-2 border-emerald-200 bg-emerald-50/40 px-4 py-3 text-amber-900 text-sm leading-relaxed whitespace-pre-wrap break-words mb-3">{!! $rev['merged_html'] !!}</div>
                                        <label class="block text-sm font-extrabold text-amber-900">Integrated chapter text (editable — locks chapter)</label>
                                    @else
                                        <label class="block text-sm font-extrabold text-amber-900">Final chapter text (locks chapter)</label>
                                        <p class="text-xs font-bold text-amber-700/80 mb-2">No accepted suggestions on this chapter. Paste your edited manuscript below. Pending suggestions must be moderated first.</p>
                                    @endif
                                    <textarea name="content" rows="8" class="w-full bg-amber-50 border-2 border-amber-100 rounded-xl px-4 py-3 text-amber-900 font-bold" required placeholder="Full chapter text">{{ old('content', $defaultPublishBody) }}</textarea>
                                    @error('content')
                                        <p class="text-sm font-bold text-red-600">{{ $message }}</p>
                                    @enderror
                                    <button type="submit" class="px-6 py-2 bg-amber-900 text-white font-extrabold rounded-xl hover:bg-black">
                                        {{ $rev['has_accepted'] ? 'Publish integrated revision & lock' : 'Upload final version & lock' }}
                                    </button>
                                </form>
                                <form action="{{ route('admin.chapters.close-story-without-merge', $openCh) }}" method="POST" class="border-t border-amber-100 pt-4" onsubmit="return confirm('Force-close this chapter without uploading merged text? This locks the chapter immediately so you can upload the next chapter.');">
                                    @csrf
                                    <button type="submit" class="px-6 py-2 bg-amber-200 text-amber-900 font-extrabold rounded-xl hover:bg-amber-300">Close without merged text</button>
                                </form>
                                <form action="{{ route('admin.chapters.extend-editing-window', $openCh) }}" method="POST" class="flex flex-wrap items-end gap-3 border-t border-amber-100 pt-4">
                                    @csrf
                                    <div>
                                        <label class="block text-xs font-bold text-amber-800 mb-1">Paid editing closes (calendar day, {{ config('app.timezone') }})</label>
                                        <p class="text-[10px] font-bold text-amber-700/60 mb-1">Readers can pay for suggestions through the end of this day. Pick an earlier date to shorten the window or a later one to extend it.</p>
                                        <input
                                            type="date"
                                            name="editing_closes_on"
                                            value="{{ old('editing_closes_on', $openCh->editing_closes_at ? $openCh->editing_closes_at->timezone(config('app.timezone'))->format('Y-m-d') : now()->timezone(config('app.timezone'))->format('Y-m-d')) }}"
                                            class="bg-amber-50 border-2 border-amber-100 rounded-lg px-3 py-2 font-bold text-amber-900"
                                            required
                                        >
                                        @error('editing_closes_on')
                                            <p class="text-xs font-bold text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <button type="submit" class="px-6 py-2 bg-white border-2 border-amber-200 text-amber-900 font-extrabold rounded-xl hover:bg-amber-50">Save close date</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if($storyChapters->isNotEmpty())
                    <div class="space-y-4">
                        <h4 class="text-lg font-extrabold text-amber-900">Existing Chapters</h4>
                        <div class="grid gap-4">
                            @foreach($storyChapters as $ch)
                                <div class="flex items-center justify-between p-4 bg-amber-50 border border-amber-100 rounded-xl">
                                    <div class="flex items-center gap-4">
                                        <span class="shrink-0 px-3 py-2 bg-amber-200 rounded-xl font-extrabold text-amber-900 text-[10px] uppercase tracking-wider text-center max-w-[8rem] leading-tight">{{ $ch->listSectionBadge() }}</span>
                                        <div>
                                            <p class="font-extrabold text-amber-900">{{ $ch->readerHeadingLine() }}</p>
                                            <p class="text-xs font-bold text-amber-700/60">Sort #{{ $ch->number }}</p>
                                            <p class="text-xs font-bold {{ $ch->is_locked ? 'text-red-500' : 'text-green-500' }}">
                                                {{ $ch->is_locked ? '🔒 Locked' : '🔓 Open for Edits' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <form action="{{ route('admin.chapters.toggle-lock', $ch) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="p-2 text-amber-600 hover:bg-amber-100 rounded-lg transition-colors" title="{{ $ch->is_locked ? 'Unlock' : 'Lock' }}">
                                                @if($ch->is_locked) 🔓 @else 🔒 @endif
                                            </button>
                                        </form>
                                        <form action="{{ route("admin.chapters.destroy", $ch) }}" method="POST" onsubmit="return confirm("Are you sure you want to delete this chapter?")">
                                            @csrf
                                            @method("DELETE")
                                            <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                                                🗑️
                                            </button>
                                        </form>
                                        <form action="{{ route("admin.chapters.archive", $ch) }}" method="POST" onsubmit="return confirm("Are you sure you want to archive this chapter?")">
                                            @csrf
                                            <button type="submit" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Archive">
                                                📦
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Upload chapter for Peter Trull Solitary Detective --}}
            <div class="bg-white border-2 border-amber-100 rounded-[2rem] p-8 shadow-sm">
                <h3 class="text-2xl font-extrabold text-amber-900 mb-2">🕵️ Peter Trull Solitary Detective</h3>
                <p class="text-amber-800/60 font-bold mb-4">Add a chapter pair (Version A and B). Previous pairs will be automatically locked.</p>
                <p class="text-sm font-bold text-amber-800/70 mb-8 leading-relaxed">
                    When replacing an existing pair, the <strong>vote winner</strong> is archived as the public “closed” record (default). Use the dropdown to <strong>override</strong> with version A or B if needed.
                </p>

                @php
                    $ptDraft = is_array($peterTrullUploadDraft ?? null) ? $peterTrullUploadDraft : [];
                    $ptDraftActive = filled($ptDraft['content_a'] ?? null) || filled($ptDraft['content_b'] ?? null) || filled($ptDraft['title'] ?? null) || array_key_exists('number', $ptDraft);
                @endphp
                @if($ptDraftActive && ! session('error'))
                    <div class="mb-6 p-4 bg-emerald-100/80 border border-emerald-300 rounded-xl text-sm font-bold text-emerald-950">
                        A previous <strong>Peter Trull</strong> upload draft is restored below (saved after an error or validation issue). It clears when a pair uploads successfully.
                    </div>
                @endif
                
                <form id="peter-trull-upload" action="{{ route('admin.chapters.store-peter-trull') }}" method="POST" class="space-y-6 mb-12">
                    @csrf
                    <div class="grid gap-6 md:grid-cols-3">
                        <div>
                            <label class="block text-amber-900 font-extrabold mb-2">Title <span class="text-amber-600 font-bold normal-case">(optional)</span></label>
                            <input type="text" name="title" value="{{ old('title', $ptDraft['title'] ?? '') }}" class="w-full bg-amber-50 border-2 border-amber-100 rounded-xl px-4 py-3 text-amber-900 focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none transition-all font-bold" placeholder="Optional — blank uses chapter number in lists">
                        </div>
                        <div>
                            <label class="block text-amber-900 font-extrabold mb-2">List section</label>
                            <select name="list_section" class="w-full bg-amber-50 border-2 border-amber-100 rounded-xl px-4 py-3 text-amber-900 focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none transition-all font-bold" required>
                                @php $ptLs = old('list_section', $ptDraft['list_section'] ?? 'chapter'); @endphp
                                <option value="cold_open" @selected($ptLs === 'cold_open')>Cold open</option>
                                <option value="prolog" @selected($ptLs === 'prolog')>Prolog</option>
                                <option value="chapter" @selected($ptLs === 'chapter')>Chapter</option>
                                <option value="epilog" @selected($ptLs === 'epilog')>Epilog</option>
                            </select>
                            <p class="text-xs font-bold text-amber-800/50 mt-2">Vote list order matches the main book: cold open → prolog → chapters → epilog.</p>
                        </div>
                        <div>
                            <label class="block text-amber-900 font-extrabold mb-2">Sort number</label>
                            @php
                                $peterNextNum = $peterChapters->isEmpty() ? 1 : ((int) $peterChapters->max('number')) + 1;
                            @endphp
                            <input type="number" name="number" value="{{ old('number', array_key_exists('number', $ptDraft) ? $ptDraft['number'] : $peterNextNum) }}" min="0" class="w-full bg-amber-50 border-2 border-amber-100 rounded-xl px-4 py-3 text-amber-900 focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none transition-all font-bold" required>
                            <p class="text-xs font-bold text-amber-800/50 mt-2">Within the same section, lower numbers appear first.</p>
                        </div>
                    </div>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-amber-900 font-extrabold mb-2">Version A Content</label>
                            <textarea name="content_a" rows="6" class="w-full bg-amber-50 border-2 border-amber-100 rounded-xl px-4 py-3 text-amber-900 focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none transition-all font-bold" required>{{ old('content_a', $ptDraft['content_a'] ?? '') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-amber-900 font-extrabold mb-2">Version B Content</label>
                            <textarea name="content_b" rows="6" class="w-full bg-amber-50 border-2 border-amber-100 rounded-xl px-4 py-3 text-amber-900 focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none transition-all font-bold" required>{{ old('content_b', $ptDraft['content_b'] ?? '') }}</textarea>
                        </div>
                    </div>
                    <div>
                        <label class="block text-amber-900 font-extrabold mb-2">Archive winner when replacing this slot</label>
                        <select name="archive_winning_version" class="w-full max-w-md bg-amber-50 border-2 border-amber-100 rounded-xl px-4 py-3 text-amber-900 font-bold">
                            <option value="auto" @selected(old('archive_winning_version', $ptDraft['archive_winning_version'] ?? 'auto') === 'auto')>Auto — highest vote count</option>
                            <option value="A" @selected(old('archive_winning_version', $ptDraft['archive_winning_version'] ?? '') === 'A')>Force version A</option>
                            <option value="B" @selected(old('archive_winning_version', $ptDraft['archive_winning_version'] ?? '') === 'B')>Force version B</option>
                        </select>
                    </div>
                    <label class="inline-flex items-center gap-3 px-4 py-3 rounded-xl border border-amber-200 bg-amber-50/70">
                        <input
                            type="checkbox"
                            name="is_pilot"
                            value="1"
                            class="rounded border-amber-300 text-amber-700 focus:ring-amber-500"
                            @checked((bool) old('is_pilot', $ptDraft['is_pilot'] ?? false))
                        >
                        <span class="text-sm font-bold text-amber-900">
                            Mark as pilot pair (close by votes at {{ config('peter_trull.pilot.close_after_votes', 50) }} total votes across A+B)
                        </span>
                    </label>
                    <button type="submit" class="px-8 py-3 bg-green-600 text-white font-extrabold rounded-xl hover:bg-green-700 transition-all shadow-lg shadow-green-600/20">
                        Upload Pair & Lock Previous
                    </button>
                </form>

                @if($peterChapters->isNotEmpty())
                    <div class="space-y-4">
                        <h4 class="text-lg font-extrabold text-amber-900">Existing Chapter Pairs</h4>
                        <div class="grid gap-4">
                            @foreach($peterChapters->groupBy(fn ($c) => $c->votePairGroupKey()) as $pairKey => $vers)
                                @php
                                    $ptRep = $vers->first();
                                    $ptPairHasUnlocked = $vers->contains(fn ($v) => ! $v->is_locked);
                                @endphp
                                <div class="p-4 bg-amber-50 border border-amber-100 rounded-xl space-y-4">
                                    <div class="flex flex-wrap items-start justify-between gap-4">
                                        <div class="flex items-center gap-4">
                                            <span class="shrink-0 px-3 py-2 bg-amber-200 rounded-xl font-extrabold text-amber-900 text-[10px] uppercase tracking-wider text-center max-w-[8rem] leading-tight">{{ $ptRep->listSectionBadge() }}</span>
                                            <div>
                                                <p class="font-extrabold text-amber-900">{{ $ptRep->readerHeadingLine() }}</p>
                                                <p class="text-xs font-bold text-amber-700/60">Sort #{{ $ptRep->number }}</p>
                                                <p class="text-xs font-bold {{ $vers->every(fn ($v) => $v->is_locked) ? 'text-red-500' : 'text-green-500' }}">
                                                    {{ $vers->every(fn ($v) => $v->is_locked) ? '🔒 Locked' : '🔓 Open for Voting' }}
                                                </p>
                                                @if($ptRep->isPilotPeterTrullChapter())
                                                    @php
                                                        $ptPilotCap = max(1, (int) config('peter_trull.pilot.close_after_votes', 50));
                                                        $ptPilotVotes = $ptRep->pilotPeterTrullVotesTotal();
                                                    @endphp
                                                    <p class="text-xs font-bold text-amber-800 mt-1">Pilot pair: closes at {{ $ptPilotCap }} total votes (A+B). Current: {{ $ptPilotVotes }}/{{ $ptPilotCap }}</p>
                                                @elseif($ptRep->editing_closes_at)
                                                    <p class="text-xs font-bold text-amber-800 mt-1">Voting closes (calendar day, {{ config('app.timezone') }}): {{ $ptRep->editing_closes_at->timezone(config('app.timezone'))->format('M j, Y') }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <form action="{{ route('admin.chapters.toggle-lock', $ptRep) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="p-2 text-amber-600 hover:bg-amber-100 rounded-lg transition-colors" title="{{ $ptRep->is_locked ? 'Unlock' : 'Lock' }}">
                                                    @if($ptRep->is_locked) 🔓 @else 🔒 @endif
                                                </button>
                                            </form>
                                            @foreach($vers as $v)
                                                <form action="{{ route('admin.chapters.destroy', $v) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this version?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete Version {{ $v->version }}">
                                                        🗑️ ({{ $v->version }})
                                                    </button>
                                                </form>
                                            @endforeach
                                        </div>
                                    </div>
                                    @if($ptPairHasUnlocked && ! $ptRep->isPilotPeterTrullChapter())
                                        <form action="{{ route('admin.chapters.extend-editing-window', $ptRep) }}" method="POST" class="flex flex-wrap items-end gap-3 border-t border-amber-200/60 pt-4">
                                            @csrf
                                            <div>
                                                <label class="block text-xs font-bold text-amber-800 mb-1">Voting closes (calendar day, {{ config('app.timezone') }})</label>
                                                <p class="text-[10px] font-bold text-amber-700/60 mb-1">Applies to both Version A and B. Same as manuscript: end of the chosen day.</p>
                                                <input
                                                    type="date"
                                                    name="editing_closes_on"
                                                    value="{{ old('editing_closes_on', $ptRep->editing_closes_at ? $ptRep->editing_closes_at->timezone(config('app.timezone'))->format('Y-m-d') : now()->timezone(config('app.timezone'))->format('Y-m-d')) }}"
                                                    class="bg-white border-2 border-amber-100 rounded-lg px-3 py-2 font-bold text-amber-900"
                                                    required
                                                >
                                                @error('editing_closes_on')
                                                    <p class="text-xs font-bold text-red-600 mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>
                                            <button type="submit" class="px-6 py-2 bg-white border-2 border-amber-200 text-amber-900 font-extrabold rounded-xl hover:bg-amber-50">Save voting close date</button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
