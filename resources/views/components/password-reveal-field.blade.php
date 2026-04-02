@props([
    'name',
    'id',
    'autocomplete' => 'current-password',
    'required' => false,
    'placeholder' => null,
])

<div class="flex items-center gap-2" x-data="{ showPassword: false }">
    <input
        :type="showPassword ? 'text' : 'password'"
        name="{{ $name }}"
        id="{{ $id }}"
        @if ($required) required @endif
        autocomplete="{{ $autocomplete }}"
        @if (filled($placeholder)) placeholder="{{ $placeholder }}" @endif
        {{ $attributes->merge(['class' => 'min-w-0 flex-1 rounded-xl border-amber-200 shadow-sm focus:border-amber-500 focus:ring-amber-500']) }}
    />
    <button
        type="button"
        class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-amber-200 bg-white text-amber-800 shadow-sm hover:bg-amber-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2 sm:h-10 sm:w-10"
        @click="showPassword = !showPassword"
        :aria-label="showPassword ? 'Hide password' : 'Show password'"
        :aria-pressed="showPassword ? 'true' : 'false'"
    >
        <svg x-show="!showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
        </svg>
        <svg x-show="showPassword" x-cloak class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
        </svg>
    </button>
</div>
