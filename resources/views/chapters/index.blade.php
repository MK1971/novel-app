@php
    $layout = auth()->check() ? 'app-layout' : 'guest-layout';
@endphp

<x-dynamic-component :component="$layout">
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-3xl text-amber-900 leading-tight">
                    The Book With No Name
                </h2>
                <p class="text-amber-800/60 font-bold mt-1">Read the story and shape the narrative.</p>
            </div>
        </div>
    </x-slot>

    {{-- Reading Progress Bar --}}
    <div class="fixed top-0 left-0 w-full h-1 bg-amber-100 z-[100]">
        <div id="reading-progress" class="h-full bg-amber-600 transition-all duration-200" style="width: 0%"></div>
    </div>

    <div class="py-12">
        @if (session('success'))
            <div class="mb-8 p-4 bg-green-100 text-green-700 rounded-2xl border border-green-200 shadow-sm font-bold">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-8 p-4 bg-red-100 text-red-700 rounded-2xl border border-red-200 shadow-sm font-bold">{{ session('error') }}</div>
        @endif

        <div class="max-w-5xl mx-auto space-y-16">
            @forelse($chapters ?? [] as $chapter)
                <div 
                    id="chapter-{{ $chapter->id }}" 
                    class="chapter-container bg-white border border-amber-100 shadow-sm rounded-[3rem] p-12 md:p-16 relative overflow-hidden transition-all duration-500"
                    data-chapter-id="{{ $chapter->id }}"
                >
                    {{-- Decorative background number --}}
                    <div class="absolute -top-10 -right-10 text-[15rem] font-black text-amber-500/5 select-none">
                        {{ $chapter->number }}
                    </div>
                    
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-10">
                            <div class="flex items-center gap-4">
                                <span class="px-4 py-1 bg-amber-500 text-black text-xs font-black rounded-full uppercase tracking-widest">Chapter {{ $chapter->number }}</span>
                                <span class="text-amber-800/60 font-bold">Version {{ $chapter->version }}</span>
                            </div>
                            
                            <div class="flex items-center gap-4">
                                @if($chapter->is_locked)
                                    <div class="flex items-center gap-6 text-[10px] font-black uppercase tracking-widest bg-amber-50/50 px-6 py-3 rounded-2xl border border-amber-100/50">
                                        <div class="flex flex-col items-center">
                                            <span class="text-amber-900 text-lg mb-0.5">{{ $chapter->statistics->total_reads ?? 0 }}</span>
                                            <span class="text-amber-900/40">Reads</span>
                                        </div>
                                        <div class="w-px h-8 bg-amber-200/50"></div>
                                        <div class="flex flex-col items-center">
                                            <span class="text-green-600 text-lg mb-0.5">{{ $chapter->statistics->accepted_edits ?? 0 }}</span>
                                            <span class="text-amber-900/40">Accepted</span>
                                        </div>
                                        <div class="w-px h-8 bg-amber-200/50"></div>
                                        <div class="flex flex-col items-center">
                                            <span class="text-amber-600 text-lg mb-0.5">{{ $chapter->statistics->total_edits ?? 0 }}</span>
                                            <span class="text-amber-900/40">Total Edits</span>
                                        </div>
                                        <div class="w-px h-8 bg-amber-200/50"></div>
                                        <div class="flex flex-col items-center">
                                            <span class="text-red-600 text-lg mb-0.5">{{ $chapter->statistics->rejected_edits ?? 0 }}</span>
                                            <span class="text-amber-900/40">Rejected</span>
                                        </div>
                                        <div class="w-px h-8 bg-amber-200/50"></div>
                                        <div class="flex flex-col items-center">
                                            <span class="text-red-700 text-lg mb-0.5">🔒</span>
                                            <span class="text-red-700">Locked</span>
                                        </div>
                                    </div>
                                    <button 
                                        onclick="toggleChapter({{ $chapter->id }})"
                                        class="p-3 bg-amber-50 rounded-2xl text-amber-900 hover:bg-amber-100 transition-all"
                                        id="toggle-btn-{{ $chapter->id }}"
                                    >
                                        <svg class="w-6 h-6 transform transition-transform duration-300" id="icon-{{ $chapter->id }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                @else
                                    <div class="px-6 py-3 bg-green-100 rounded-2xl border border-green-200 text-green-700 text-xs font-black uppercase tracking-widest flex items-center gap-2">
                                        <span class="relative flex h-2 w-2">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                                        </span>
                                        Open for Edits
                                    </div>
                                @endif
                            </div>
                        </div>

                        <h3 class="text-4xl font-extrabold text-amber-900 mb-8">{{ $chapter->title }}</h3>
                        
                        <div 
                            id="content-{{ $chapter->id }}" 
                            class="chapter-content max-w-none text-amber-900/80 leading-[2.2] font-medium text-left mb-12 {{ $chapter->is_locked ? 'hidden' : '' }}"
                        >
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
                                                    onclick="openInlineEdit({{ $chapter->id }}, {{ $index }}, '{{ addslashes(trim($paragraph)) }}')"
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

                        <div class="flex items-center justify-between pt-8 border-t border-amber-100">
                            <div class="text-amber-800/40 text-sm font-bold">
                                Published {{ $chapter->created_at->format('M d, Y') }}
                            </div>
                            
                            @if(!$chapter->is_locked)
                                <a href="{{ route('chapters.show', $chapter) }}" class="inline-flex items-center px-10 py-5 bg-amber-500 text-black font-extrabold rounded-2xl hover:bg-amber-600 transition-all shadow-xl shadow-amber-500/30 transform hover:-translate-y-1">
                                    Suggest an Edit
                                    <svg class="w-6 h-6 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                            @else
                                <div class="text-amber-800/40 font-black uppercase tracking-widest text-xs">
                                    Final Version
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-32 bg-white border border-amber-100 rounded-[3rem] shadow-sm">
                    <div class="w-20 h-20 bg-amber-100 rounded-3xl flex items-center justify-center mx-auto mb-8">
                        <svg class="w-10 h-10 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    <h3 class="text-2xl font-extrabold text-amber-900 mb-2">The ink is still drying...</h3>
                    <p class="text-amber-800/50 text-lg font-bold">No chapters have been published yet. Check back soon!</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Inline Edit Modal --}}
    @auth
    <div id="inline-edit-modal" class="fixed inset-0 bg-amber-900/80 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-[3rem] w-full max-w-2xl p-12 shadow-2xl">
            <h3 class="text-2xl font-extrabold text-amber-900 mb-8">Suggest Paragraph Edit</h3>
            <form id="inline-edit-form" class="space-y-8">
                <input type="hidden" id="edit-chapter-id" name="chapter_id">
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
    @endauth

    <script>
        const progressBar = document.getElementById('reading-progress');
        let lastSavedProgress = 0;
        let currentChapterId = null;
        let selectionButton = null;

        // Text Selection Logic
        document.addEventListener('mouseup', function(e) {
            const selection = window.getSelection();
            const selectedText = selection.toString().trim();
            
            if (selectionButton) {
                selectionButton.remove();
                selectionButton = null;
            }

            if (selectedText.length > 0) {
                const range = selection.getRangeAt(0);
                const container = range.commonAncestorContainer;
                
                // Ensure the selection is within an unlocked chapter content
                let chapterContent = container.nodeType === 3 ? container.parentElement : container;
                while (chapterContent && !chapterContent.classList.contains('chapter-content')) {
                    chapterContent = chapterContent.parentElement;
                }

                if (chapterContent && !chapterContent.classList.contains('hidden')) {
                    const chapterId = chapterContent.id.replace('content-', '');
                    
                    selectionButton = document.createElement('button');
                    selectionButton.innerHTML = '✨ Suggest Edit';
                    selectionButton.className = 'fixed z-[200] px-4 py-2 bg-amber-900 text-white text-xs font-black rounded-full shadow-2xl hover:bg-black transition-all transform -translate-x-1/2 -translate-y-full mt-[-10px]';
                    
                    const rect = range.getBoundingClientRect();
                    selectionButton.style.left = (rect.left + rect.width / 2) + 'px';
                    selectionButton.style.top = rect.top + window.scrollY + 'px';
                    
                    selectionButton.onclick = function() {
                        openInlineEdit(chapterId, 0, selectedText);
                        selection.removeAllRanges();
                        selectionButton.remove();
                        selectionButton = null;
                    };
                    
                    document.body.appendChild(selectionButton);
                }
            }
        });

        function toggleChapter(id) {
            const content = document.getElementById('content-' + id);
            const icon = document.getElementById('icon-' + id);
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                icon.classList.add('rotate-180');
            } else {
                content.classList.add('hidden');
                icon.classList.remove('rotate-180');
            }
        }

        @auth
        const modal = document.getElementById('inline-edit-modal');
        const form = document.getElementById('inline-edit-form');

        function openInlineEdit(chapterId, number, text) {
            document.getElementById('edit-chapter-id').value = chapterId;
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
            const chapterId = data.chapter_id;

            fetch(`/chapters/${chapterId}/inline-edit`, {
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

        // Reading Progress Logic
        window.addEventListener('scroll', function() {
            const height = document.documentElement.scrollHeight - window.innerHeight;
            if (height <= 0) return;
            
            const scrollTop = window.scrollY;
            const progress = (scrollTop / height) * 100;
            progressBar.style.width = progress + '%';

            // Identify which chapter is in view
            const chapters = document.querySelectorAll('.chapter-container');
            let activeChapterId = null;
            
            chapters.forEach(chapter => {
                const rect = chapter.getBoundingClientRect();
                if (rect.top < window.innerHeight / 2 && rect.bottom > window.innerHeight / 2) {
                    activeChapterId = chapter.dataset.chapterId;
                }
            });

            if (activeChapterId && (activeChapterId !== currentChapterId || Math.abs(scrollTop - lastSavedProgress) >= 500)) {
                saveProgress(activeChapterId, scrollTop);
                currentChapterId = activeChapterId;
                lastSavedProgress = scrollTop;
            }
        });

        function saveProgress(chapterId, scrollTop) {
            fetch(`/chapters/${chapterId}/track-progress`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ scroll_position: scrollTop })
            });
        }

        // Restore scroll position for the last read chapter
        const savedProgress = @json($progress);
        const lastChapterId = Object.keys(savedProgress).pop();
        if (lastChapterId && savedProgress[lastChapterId] > 0) {
            const element = document.getElementById('chapter-' + lastChapterId);
            if (element) {
                element.scrollIntoView();
                window.scrollBy(0, savedProgress[lastChapterId]);
            }
        }
        @endauth
    </script>
</x-dynamic-component>
