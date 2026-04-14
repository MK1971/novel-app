<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-stone-900 dark:text-stone-100 leading-tight">
            Public edit feed
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 text-green-800 px-4 py-3 text-sm font-semibold">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="rounded-lg border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm font-semibold">
                    {{ session('error') }}
                </div>
            @endif

            @if ($items->isEmpty())
                <div class="p-6 rounded-xl bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-700 text-stone-700 dark:text-stone-200">
                    <p class="font-semibold">No accepted public edits yet.</p>
                    <p class="mt-2 text-sm text-stone-600 dark:text-stone-300">When accepted edits are published, this feed becomes the public record of what changed and why.</p>
                    <a href="{{ route('chapters.index') }}" class="mt-4 inline-flex items-center px-4 py-2 rounded-lg bg-amber-500 text-black text-sm font-semibold hover:bg-amber-400">
                        Enter the manuscript
                    </a>
                </div>
            @else
                @foreach ($items as $item)
                    <article class="p-5 rounded-xl bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-700 shadow-sm space-y-3">
                        <div class="flex flex-wrap items-center gap-2 text-sm">
                            <span class="font-bold text-stone-900 dark:text-stone-100">{{ $item['chapter_label'] }}</span>
                            <span class="text-stone-600 dark:text-stone-300">by {{ $item['user_name'] }}</span>
                            <span class="px-2 py-0.5 rounded-full bg-stone-100 dark:bg-stone-800 border border-stone-200 dark:border-stone-600 text-xs uppercase font-semibold text-stone-800 dark:text-stone-100">{{ $item['status'] }}</span>
                            <span class="text-stone-500 dark:text-stone-400">{{ $item['updated_at']->diffForHumans() }}</span>
                        </div>
                        <p class="text-stone-800 dark:text-stone-200">{{ $item['excerpt'] }}</p>

                        <div class="space-y-2">
                            @foreach ($item['feedback'] as $feedback)
                                <div class="text-sm text-stone-700 dark:text-stone-200 bg-stone-50 dark:bg-stone-800 border border-stone-200 dark:border-stone-600 rounded-lg px-3 py-2">
                                    <span class="font-semibold text-stone-900 dark:text-stone-100">{{ $feedback->user?->name ?? 'Unknown' }}:</span>
                                    {{ $feedback->message }}
                                </div>
                            @endforeach
                        </div>

                        @auth
                            @if ((int) auth()->id() !== (int) $item['user_id'])
                            <form method="POST" action="{{ route('edits.public.feedback') }}" class="space-y-2">
                                @csrf
                                <input type="hidden" name="kind" value="{{ $item['kind'] }}">
                                <input type="hidden" name="id" value="{{ $item['id'] }}">
                                <label class="block text-xs font-semibold text-stone-700 dark:text-stone-300">Add feedback</label>
                                <textarea name="message" rows="2" required class="w-full rounded-lg border-stone-300 dark:border-stone-600 bg-white dark:bg-stone-950 text-stone-900 dark:text-stone-100 placeholder:text-stone-400 focus:border-amber-500 focus:ring-amber-500 text-sm"></textarea>
                                <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-amber-500 text-black font-semibold text-sm hover:bg-amber-400">
                                    Post feedback
                                </button>
                            </form>
                            @else
                                <p class="text-xs font-semibold text-stone-600 dark:text-stone-300">You cannot post feedback on your own edit.</p>
                            @endif
                        @endauth
                    </article>
                @endforeach
            @endif
        </div>
    </div>
</x-app-layout>
