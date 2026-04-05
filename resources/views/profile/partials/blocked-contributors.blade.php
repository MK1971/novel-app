<section id="blocked-contributors" class="mt-10 border-t border-amber-100 dark:border-stone-700 pt-10">
    <h3 class="text-xl font-extrabold text-amber-900 dark:text-amber-50 mb-2">{{ __('Blocked contributors') }}</h3>
    <p class="text-amber-800/70 dark:text-stone-400 font-bold text-sm mb-6 max-w-xl leading-relaxed">
        {{ __('These people cannot see your public profile and you cannot see theirs while the block is active.') }}
    </p>
    @if ($blockedContributors->isEmpty())
        <p class="text-sm font-bold text-amber-800/50 dark:text-stone-500">{{ __('No active blocks.') }}</p>
    @else
        <ul class="space-y-3 max-w-xl">
            @foreach ($blockedContributors as $block)
                @php $blocked = $block->blocked; @endphp
                @if ($blocked)
                    <li class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-amber-100 dark:border-stone-700 bg-amber-50/40 dark:bg-stone-800/50 px-4 py-3">
                        <span class="font-bold text-amber-900 dark:text-amber-100">{{ $blocked->name }}</span>
                        <form method="post" action="{{ route('profile.blocks.destroy', $blocked) }}" class="inline">
                            @csrf
                            @method('delete')
                            <x-secondary-button type="submit" class="text-sm py-2">
                                {{ __('Unblock') }}
                            </x-secondary-button>
                        </form>
                    </li>
                @endif
            @endforeach
        </ul>
    @endif
</section>
