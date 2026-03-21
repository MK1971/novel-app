@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-amber-900 mb-2">{{ $chapter->title }}</h1>
            <p class="text-amber-700">Chapter {{ $chapter->number }}</p>
        </div>

        <!-- Reading Progress Bar -->
        <div class="fixed top-0 left-0 w-full h-1 bg-amber-100 z-50">
            <div id="reading-progress" class="h-full bg-amber-600 transition-all duration-200" style="width: 0%"></div>
        </div>

        <div id="chapter-content" class="prose prose-lg max-w-none text-amber-900 leading-relaxed text-left">
            {!! nl2br(e($chapter->content)) !!}
        </div>

        <!-- Chapter Stats -->
        <div class="mt-12 p-6 bg-amber-50 rounded-xl border border-amber-100">
            <h3 class="text-xl font-bold text-amber-900 mb-4">Chapter Statistics</h3>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div class="text-center">
                    <p class="text-sm text-amber-600">Reads</p>
                    <p class="text-2xl font-bold text-amber-900">{{ $chapter->statistics->total_reads ?? 0 }}</p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-amber-600">Edits</p>
                    <p class="text-2xl font-bold text-amber-900">{{ $chapter->statistics->total_edits ?? 0 }}</p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-amber-600">Accepted</p>
                    <p class="text-2xl font-bold text-amber-900">{{ $chapter->statistics->accepted_edits ?? 0 }}</p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-amber-600">Rejected</p>
                    <p class="text-2xl font-bold text-amber-900">{{ $chapter->statistics->rejected_edits ?? 0 }}</p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-amber-600">Votes</p>
                    <p class="text-2xl font-bold text-amber-900">{{ $chapter->statistics->total_votes ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

@auth
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const progressBar = document.getElementById('reading-progress');
        const chapterId = {{ $chapter->id }};
        let lastScrollTop = 0;
        let ticking = false;

        // Restore scroll position
        const savedProgress = {{ $progress }};
        if (savedProgress > 0) {
            window.scrollTo(0, savedProgress);
        }

        window.addEventListener('scroll', function() {
            lastScrollTop = window.scrollY;
            if (!ticking) {
                window.requestAnimationFrame(function() {
                    updateProgress(lastScrollTop);
                    ticking = false;
                });
                ticking = true;
            }
        });

        function updateProgress(scrollTop) {
            const height = document.documentElement.scrollHeight - window.innerHeight;
            const progress = (scrollTop / height) * 100;
            progressBar.style.width = progress + '%';

            // Save progress every 5% or when reaching bottom
            if (Math.abs(progress - lastSavedProgress) >= 5 || progress >= 99) {
                saveProgress(scrollTop);
                lastSavedProgress = progress;
            }
        }

        let lastSavedProgress = (savedProgress / (document.documentElement.scrollHeight - window.innerHeight)) * 100 || 0;

        function saveProgress(scrollTop) {
            fetch(`/chapters/${chapterId}/track-progress`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ scroll_position: scrollTop })
            });
        }
    });
</script>
@endauth
@endsection
