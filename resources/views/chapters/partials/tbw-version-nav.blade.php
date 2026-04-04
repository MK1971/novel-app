@php
    $showNav = ($tbwArchiveSiblings->isNotEmpty())
        || $tbwLiveForNav
        || $tbwOtherArchiveSiblings->isNotEmpty();
@endphp
@if($showNav)
    <nav class="mb-8 flex flex-wrap items-center gap-2 border-b border-amber-100 pb-4" aria-label="Chapter versions">
        <span class="text-xs font-black uppercase tracking-widest text-amber-800/60 w-full sm:w-auto mb-1 sm:mb-0">Versions</span>
        @if(! $chapter->is_archived)
            <span class="inline-flex items-center px-4 py-2 rounded-full bg-amber-500 text-black text-sm font-black">This release</span>
            @foreach($tbwArchiveSiblings as $arch)
                <a href="{{ route('chapters.show', $arch) }}" class="inline-flex items-center px-4 py-2 rounded-full bg-amber-50 text-amber-900 text-sm font-bold border border-amber-200 hover:bg-amber-100 transition-colors">
                    Earlier — {{ $arch->displayTitle() }}
                    @if($arch->published_at)
                        <span class="ml-1 text-amber-700/80 font-semibold text-xs">({{ $arch->published_at->format('M j, Y') }})</span>
                    @endif
                </a>
            @endforeach
        @else
            @if($tbwLiveForNav)
                <a href="{{ route('chapters.show', $tbwLiveForNav) }}" class="inline-flex items-center px-4 py-2 rounded-full bg-amber-500 text-black text-sm font-black hover:bg-amber-400 transition-colors">
                    Current release
                </a>
            @endif
            <span class="inline-flex items-center px-4 py-2 rounded-full bg-amber-100 text-amber-950 text-sm font-black border border-amber-300">Archived copy</span>
            @foreach($tbwOtherArchiveSiblings as $arch)
                <a href="{{ route('chapters.show', $arch) }}" class="inline-flex items-center px-4 py-2 rounded-full bg-amber-50 text-amber-900 text-sm font-bold border border-amber-200 hover:bg-amber-100 transition-colors">
                    Other archive — {{ $arch->displayTitle() }}
                </a>
            @endforeach
        @endif
    </nav>
@endif
