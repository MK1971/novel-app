<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-3xl text-amber-900 leading-tight">
                    {{ $chapter->title }}
                </h2>
                <p class="text-amber-800/60 font-bold mt-1">Chapter {{ $chapter->number }} • The Book With No Name</p>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('chapters.index') }}" class="px-6 py-3 bg-amber-50 text-amber-900 text-sm font-black rounded-xl hover:bg-amber-100 transition-all uppercase tracking-widest">
                    Back to Chapters
                </a>
                @auth
                    <a href="{{ route('edits.create', $chapter->id) }}" class="px-8 py-3 bg-amber-900 text-white text-sm font-black rounded-xl hover:bg-black transition-all shadow-xl shadow-amber-900/20 transform hover:-translate-y-0.5 uppercase tracking-widest">
                        Suggest Edit
                    </a>
                @endauth
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Reading Progress Bar --}}
            <div class="fixed top-0 left-0 w-full h-1 bg-amber-100 z-50">
                <div id="reading-progress" class="h-full bg-amber-600 transition-all duration-200" style="width: 0%"></div>
            </div>

            <div class="bg-white border border-amber-100 shadow-sm rounded-[3rem] p-12 mb-12 relative overflow-hidden">
                {{-- Decorative element --}}
                <div class="absolute top-0 right-0 w-64 h-64 bg-amber-50 rounded-full -mr-32 -mt-32 opacity-50"></div>
                
                <div id="chapter-content" class="prose prose-lg max-w-none text-amber-900 leading-relaxed text-left relative z-10">
                    @php
                        $paragraphs = explode("\n", $chapter->content);
                    @endphp
                    @foreach($paragraphs as $index => $paragraph)
                        @if(trim($paragraph))
                            <p class="mb-6 relative group">
                                {{ $paragraph }}
                                @auth
                                    <button 
                                        onclick="openInlineEdit({{ $index }}, '{{ addslashes(trim($paragraph)) }}')"
                                        class="absolute -right-12 top-0 opacity-0 group-hover:opacity-100 transition-opacity p-2 text-amber-400 hover:text-amber-600"
                                        title="Suggest edit for this paragraph"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                @endauth
                            </p>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Chapter Stats --}}
            <div class="bg-amber-900 rounded-[3rem] p-12 text-amber-50 shadow-xl shadow-amber-900/20">
                <h3 class="text-xl font-extrabold mb-10 flex items-center gap-3">
                    <span class="w-8 h-8 bg-amber-500 rounded-lg flex items-center justify-center text-black text-sm">📊</span>
                    Chapter Statistics
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-8">
                    <div class="bg-white/5 rounded-2xl p-6 border border-white/10">
                        <p class="text-[10px] font-black uppercase tracking-widest text-amber-50/40 mb-2">Reads</p>
                        <p class="text-3xl font-black">{{ number_format($chapter->statistics->total_reads ?? 0) }}</p>
                    </div>
                    <div class="bg-white/5 rounded-2xl p-6 border border-white/10">
                        <p class="text-[10px] font-black uppercase tracking-widest text-amber-50/40 mb-2">Edits</p>
                        <p class="text-3xl font-black">{{ number_format($chapter->statistics->total_edits ?? 0) }}</p>
                    </div>
                    <div class="bg-white/5 rounded-2xl p-6 border border-white/10">
                        <p class="text-[10px] font-black uppercase tracking-widest text-amber-50/40 mb-2">Accepted</p>
                        <p class="text-3xl font-black text-amber-400">{{ number_format($chapter->statistics->accepted_edits ?? 0) }}</p>
                    </div>
                    <div class="bg-white/5 rounded-2xl p-6 border border-white/10">
                        <p class="text-[10px] font-black uppercase tracking-widest text-amber-50/40 mb-2">Rejected</p>
                        <p class="text-3xl font-black text-red-400">{{ number_format($chapter->statistics->rejected_edits ?? 0) }}</p>
                    </div>
                    <div class="bg-white/5 rounded-2xl p-6 border border-white/10">
                        <p class="text-[10px] font-black uppercase tracking-widest text-amber-50/40 mb-2">Votes</p>
                        <p class="text-3xl font-black">{{ number_format($chapter->statistics->total_votes ?? 0) }}</p>
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

            fetch('{{ route('inline-edit.store', $chapter) }}', {
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
        const savedProgress = {{ $progress }};
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
</x-app-layout>
