@props(['messages'])

@php
    $list = $messages instanceof \Illuminate\Support\MessageBag
        ? $messages->all()
        : (array) ($messages ?? []);
@endphp
@if (count($list) > 0)
    <ul {{ $attributes->merge(['class' => 'text-sm text-red-600 space-y-1 font-bold']) }} role="alert">
        @foreach ($list as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
