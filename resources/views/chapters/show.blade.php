@php

    $layout = auth()->check() ? 'app-layout' : 'guest-layout';
@endphp

<x-dynamic-component :component="$layout">
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-6">
                <a href="{{ route('chapters.index') }}" class="w-12 h-12 bg-white border border-amber-200 rounded-2xl flex items-center justify-center text-amber-900 hover:bg-amber-50 transition-all shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <div>
                    <h2 class="font-extrabold text-3xl text-amber-900 leading-tight">
                        Chapter {{ $chapter->number }}: {{ $chapter->title }}
                    </h2>
                    <p class="text-amber-800/60 font-bold mt-1">Version {{ $chapter->version }} • Published {{ $chapter->created_at->format('M d, Y') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                @if($chapter->is_locked)
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-4 text-[10px] font-black uppercase tracking-widest bg-white/50 px-4 py-2 rounded-xl border border-amber-100">
                            <span class="text-green-600">{{ $stats->accepted_edits ?? 0 }} Accepted</span>
                            <span class="text-amber-600">{{ $stats->total_edits ?? 0 }} Total</span>
                            <span class="text-red-600">{{ $stats->rejected_edits ?? 0 }} Rejected</span>
                        </div>
                        <div class="px-6 py-2 bg-red-100 rounded-2xl border border-red-200 text-red-700 text-sm font-black uppercase tracking-widest">
                            🔒 Locked
                        </div>
                    </div>
                @else
                    <div class="px-4 py-2 bg-amber-100 rounded-2xl border border-amber-200/50 text-amber-900 text-sm font-bold">
                        <span class="opacity-60 mr-1">Points for accepted edit:</span>
                        <span class="text-amber-600">+1-2 pts</span>
                    </div>
                @endif
            </div>
        </div>
    </x-slot>

    {{-- Reading Progress Bar --}}
    <div class="fixed top-0 left-0 w-full h-1 bg-amber-100 z-[100]">
        <div id="reading-progress" class="h-full bg-amber-600 transition-all duration-200" style="width: 0%"></div>
    </div>

    <div class="py-12">
        <div class="grid lg:grid-cols-3 gap-12">
            {{-- Chapter Content --}}
            <div class="lg:col-span-2">
                <div class="bg-white border border-amber-100 shadow-sm rounded-[3rem] p-12 md:p-16 relative overflow-hidden">
                    {{-- Decorative background number --}}
                    <div class="absolute -top-10 -right-10 text-[15rem] font-black text-amber-500/5 select-none">
                        {{ $chapter->number }}
                    </div>
                    
                    <div class="relative z-10">
                        @if($chapter->is_locked)
                            <div class="absolute inset-0 pointer-events-none flex items-center justify-center opacity-[0.04] select-none overflow-hidden">
                                <div class="text-[20rem] font-black rotate-[-35deg] whitespace-nowrap">LOCKED</div>
                            </div>
                        @endif
                        <div id="chapter-content" class="max-w-none text-amber-900/80 leading-[2.2] font-medium text-left {{ $chapter->is_locked ? 'opacity-80 grayscale-[0.2]' : '' }}">
                            @php
                                $paragraphs = explode("\n", $chapter->content);
                            @endphp
                            @foreach($paragraphs as $index => $paragraph)
                                @if(trim($paragraph))
                                    <p class="mb-6 relative group">
                                        {{ $paragraph }}
                                        @auth
                                            @if(!$chapter->is_locked)
                                                <button 
                                                    onclick="openInlineEdit({{ $index }}, '{{ addslashes(trim($paragraph)) }}')"
                                                    class="absolute -right-8 top-0 opacity-0 group-hover:opacity-100 transition-opacity p-2 text-amber-400 hover:text-amber-600"
                                                    title="Suggest edit for this paragraph"
                                                >
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                </button>
                                            @endif
                                        @endauth
                                    </p>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar: Suggest Edit --}}
            <div class="lg:col-span-1">
                <div class="sticky top-24 space-y-8">
                    @if($chapter->is_locked)
                        <div class="bg-amber-50 border-2 border-amber-100 rounded-[3rem] p-10 text-amber-900 shadow-sm relative overflow-hidden">
                            <div class="relative z-10">
                                <div class="w-16 h-16 bg-amber-100 rounded-2xl flex items-center justify-center mb-8">
                                    <span class="text-3xl">🔒</span>
                                </div>
                                <h3 class="text-2xl font-extrabold mb-6">Chapter Locked</h3>
                                <p class="text-amber-800/60 text-lg font-bold mb-10 leading-relaxed">
                                    This chapter is now part of the permanent record. Suggestions are closed, but you can still read and enjoy the community-shaped narrative.
                                </p>
                                <a href="{{ route('chapters.index') }}" class="w-full inline-block text-center py-5 bg-amber-900 text-white text-lg font-extrabold rounded-2xl hover:bg-black transition-all shadow-xl shadow-amber-900/20 transform hover:-translate-y-1">
                                    Read Next Chapter
                                </a>
                            </div>
                        </div>
                    @else
                        @auth
                            <div class="bg-amber-900 rounded-[3rem] p-10 text-white shadow-2xl shadow-amber-900/20 relative overflow-hidden">
                                <div class="relative z-10">
                                    <h3 class="text-2xl font-extrabold mb-6">Suggest an Edit</h3>
                                    <p class="text-amber-100/70 text-sm font-bold mb-8 leading-relaxed">
                                        Help shape the narrative! For a small $2 fee, you can suggest a change to this chapter. If accepted, you'll earn 1-2 points and climb the leaderboard.
                                    </p>
                                    
                                    <form action="{{ route('payment.checkout') }}" method="POST" class="space-y-6">
                                        @csrf
                                        <input type="hidden" name="chapter_id" value="{{ $chapter->id }}">
                                        
                                        <div>
                                            <label class="block text-xs font-black uppercase tracking-widest text-amber-400 mb-3">Edit Type</label>
                                            <select name="type" class="w-full bg-white/10 border-white/20 rounded-2xl text-white focus:ring-amber-500 focus:border-amber-500 font-bold p-4 outline-none transition-all" required>
                                                <option value="writing" class="bg-amber-900">Writing Edit</option>
                                                <option value="phrase" class="bg-amber-900">Phrase Edit</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label for="edited_text" class="block text-xs font-black uppercase tracking-widest text-amber-400 mb-3">Your Edited Text</label>
                                            <textarea 
                                                name="edited_text" 
                                                id="edited_text" 
                                                rows="10" 
                                                class="w-full bg-white/10 border-white/20 rounded-2xl text-white placeholder-white/30 focus:ring-amber-500 focus:border-amber-500 font-bold p-4"
                                                placeholder="Enter your suggested edit..."
                                                required
                                            >{{ old('edited_text', '') }}</textarea>
                                            @error('edited_text')
                                                <p class="text-red-400 text-xs mt-2 font-bold">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        
                                        <button type="submit" class="w-full py-5 bg-amber-500 text-black text-lg font-extrabold rounded-2xl hover:bg-amber-600 transition-all shadow-xl shadow-amber-500/30 transform hover:-translate-y-1">
                                            Submit & Pay $2
                                        </button>
                                    </form>
                                </div>
                                {{-- Decorative circles --}}
                                <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
                            </div>
                        @else
                            <div class="bg-amber-500 rounded-[3rem] p-10 text-black shadow-2xl shadow-amber-500/20 relative overflow-hidden">
                                <div class="relative z-10">
                                    <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mb-8">
                                        <svg class="w-8 h-8 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </div>
                                    <h3 class="text-2xl font-extrabold mb-6">Want to suggest an edit?</h3>
                                    <p class="text-black/60 text-lg font-bold mb-10 leading-relaxed">
                                        Join our community of contributors to shape the story and earn your place on the leaderboard.
                                    </p>
                                    <button onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'register' }))" class="w-full py-5 bg-black text-white text-lg font-extrabold rounded-2xl hover:bg-amber-900 transition-all shadow-xl shadow-black/20 transform hover:-translate-y-1">
                                        Create Account
                                    </button>
                                    <button onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'login' }))" class="w-full mt-4 py-4 bg-transparent border-2 border-black/10 text-black text-lg font-extrabold rounded-2xl hover:bg-black/5 transition-all">
                                        Sign In
                                    </button>
                                </div>
                            </div>
                        @endauth
                    @endif

                    <div class="p-8 bg-white border border-amber-100 rounded-[2.5rem] shadow-sm">
                        <h4 class="text-xs font-black uppercase tracking-widest text-amber-900/30 mb-6">Chapter Stats</h4>
                        <div class="space-y-6">
                            <div class="flex items-center justify-between">
                                <span class="text-amber-900/60 font-bold">📖 Total Reads</span>
                                <span class="text-amber-900 font-black">{{ $stats->total_reads ?? 0 }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-amber-900/60 font-bold">✏️ Total Edits</span>
                                <span class="text-amber-900 font-black">{{ $stats->total_edits ?? 0 }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-amber-900/60 font-bold">✅ Accepted</span>
                                <span class="text-amber-900 font-black text-green-600">{{ $stats->accepted_edits ?? 0 }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-amber-900/60 font-bold">❌ Rejected</span>
                                <span class="text-amber-900 font-black text-red-600">{{ $stats->rejected_edits ?? 0 }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-amber-900/60 font-bold">🗳️ Votes</span>
                                <span class="text-amber-900 font-black">{{ $stats->total_votes ?? 0 }}</span>
                            </div>
                            <div class="pt-6 border-t border-amber-50">
                                <p class="text-xs text-amber-800/40 font-bold leading-relaxed">
                                    @if($chapter->is_locked)
                                        This chapter is locked. Final stats are preserved above.
                                    @else
                                        Accepted edits are permanently integrated into the final version of the novel.
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Feedback Form --}}
                    <div class="p-8 bg-amber-50 border border-amber-100 rounded-[2.5rem] shadow-sm">
                        <h4 class="text-xs font-black uppercase tracking-widest text-amber-900/30 mb-6">Chapter Feedback</h4>
                        <p class="text-sm text-amber-900/60 font-bold mb-6">Have thoughts on this chapter? Share them with us!</p>
                        
                        <form action="{{ route('feedback.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="chapter_id" value="{{ $chapter->id }}">
                            <input type="hidden" name="type" value="chapter">
                            
                            @guest
                                <div>
                                    <input type="email" name="email" placeholder="Your email (optional)" class="w-full bg-white border border-amber-200 rounded-xl px-4 py-2 text-sm text-amber-900 focus:ring-2 focus:ring-amber-500 outline-none transition-all">
                                </div>
                            @endguest
                            
                            <div>
                                <textarea name="content" rows="4" placeholder="Your feedback..." class="w-full bg-white border border-amber-200 rounded-xl px-4 py-2 text-sm text-amber-900 focus:ring-2 focus:ring-amber-500 outline-none transition-all" required></textarea>
                            </div>
                            
                            <button type="submit" class="w-full py-3 bg-amber-900 text-white text-sm font-black rounded-xl hover:bg-black transition-all shadow-lg shadow-amber-900/10">
                                Send Feedback
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Inline Edit Modal --}}
    @auth
    <div id="inline-edit-modal" class="fixed inset-0 bg-amber-900/80 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-[3rem] w-full max-w-2xl p-12 shadow-2xl">
            <h3 class="text-2xl font-extrabold text-amber-900 mb-8">Suggest Paragraph Edit</h3>
            <form id="inline-edit-form" class="space-y-8">
                <input type="hidden" id="paragraph-number" name="paragraph_number">
                <input type="hidden" id="original-text-input" name="original_text">
                
                <div>
                    <label class="block text-xs font-black text-amber-900/30 uppercase tracking-widest mb-4">Original Paragraph</label>
                    <div id="original-text-display" class="p-6 bg-amber-50 rounded-2xl text-amber-900/60 italic text-sm border border-amber-100"></div>
                </div>

                <div>
                    <label class="block text-xs font-black text-amber-900/30 uppercase tracking-widest mb-4">Your Suggestion</label>
                    <textarea id="suggested-text" name="suggested_text" rows="4" class="w-full bg-amber-50/50 border-2 border-amber-100 rounded-2xl px-6 py-4 text-amber-900 font-bold focus:border-amber-500 focus:ring-0 transition-all" required></textarea>
                </div>

                <div>
                    <label class="block text-xs font-black text-amber-900/30 uppercase tracking-widest mb-4">Reason (Optional)</label>
                    <input type="text" id="edit-reason" name="reason" class="w-full bg-amber-50/50 border-2 border-amber-100 rounded-2xl px-6 py-4 text-amber-900 font-bold focus:border-amber-500 focus:ring-0 transition-all">
                </div>

                <div class="flex items-center gap-4 pt-4">
                    <button type="submit" class="px-10 py-4 bg-amber-900 text-white font-extrabold rounded-2xl hover:bg-black transition-all shadow-xl shadow-amber-900/20 transform hover:-translate-y-0.5">
                        Submit Suggestion
                    </button>
                    <button type="button" onclick="closeInlineEdit()" class="px-10 py-4 bg-amber-50 text-amber-900 font-extrabold rounded-2xl hover:bg-amber-100 transition-all">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('inline-edit-modal');
        const form = document.getElementById('inline-edit-form');
        const progressBar = document.getElementById('reading-progress');
        const chapterId = {{ $chapter->id }};
        let lastSavedProgress = 0;

        function openInlineEdit(number, text) {
            document.getElementById('paragraph-number').value = number;
            document.getElementById('original-text-input').value = text;
            document.getElementById('original-text-display').innerText = text;
            document.getElementById('suggested-text').value = text;
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeInlineEdit() {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            data.chapter_id = chapterId;

            fetch('{{ route('inline-edit.store', ['chapter' => $chapter->id]) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('Suggestion submitted! You will earn 1 point if accepted.');
                    closeInlineEdit();
                }
            })
            .catch(error => console.error('Error:', error));
        });

        // Restore scroll position
        const savedProgress = {{ $progress ?? 0 }};
        if (savedProgress > 0) {
            window.scrollTo(0, savedProgress);
        }

        window.addEventListener('scroll', function() {
            const height = document.documentElement.scrollHeight - window.innerHeight;
            if (height <= 0) return;
            
            const scrollTop = window.scrollY;
            const progress = (scrollTop / height) * 100;
            progressBar.style.width = progress + '%';

            if (Math.abs(scrollTop - lastSavedProgress) >= 500 || progress >= 99) {
                saveProgress(scrollTop);
                lastSavedProgress = scrollTop;
            }
        });

        function saveProgress(scrollTop) {
            fetch(`/chapters/${chapterId}/track-progress`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ scroll_position: scrollTop })
            });
        }
    </script>
    @endauth
</x-dynamic-component>
