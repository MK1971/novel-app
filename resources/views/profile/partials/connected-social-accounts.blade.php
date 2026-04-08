@php
    $accounts = $user->socialAccounts()->orderBy('provider')->get();
@endphp
@if($accounts->isNotEmpty())
    <section class="border-t border-amber-100 pt-8 mt-8">
        <h4 class="text-lg font-extrabold text-amber-900 mb-2">Connected sign-in</h4>
        <p class="text-sm font-bold text-amber-800/70 mb-4 leading-relaxed">
            You can sign in with the providers below. Disconnect only if you have another way to sign in (another linked provider or a password).
        </p>
        <ul class="space-y-3">
            @foreach($accounts as $account)
                <li class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-amber-100 bg-amber-50/50 px-4 py-3">
                    <span class="text-sm font-extrabold text-amber-900 capitalize">{{ $account->provider }}</span>
                    <form method="post" action="{{ route('profile.social.disconnect', ['provider' => $account->provider]) }}" class="inline">
                        @csrf
                        @method('delete')
                        <button
                            type="submit"
                            class="text-sm font-black text-red-700 hover:text-red-900 underline decoration-2 underline-offset-2"
                        >
                            Disconnect
                        </button>
                    </form>
                </li>
            @endforeach
        </ul>
        <x-input-error :messages="$errors->get('social')" class="mt-3" />
    </section>
@endif
