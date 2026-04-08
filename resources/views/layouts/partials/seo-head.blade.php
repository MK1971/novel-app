@php
    $desc = $metaDescription ?? config('seo.default_description');
    $titleText = $pageTitle ?? config('app.name');
    $ogImage = config('seo.og_image_url');
@endphp
<meta name="description" content="{{ $desc }}">
<link rel="canonical" href="{{ url()->current() }}">
<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ config('app.name') }}">
<meta property="og:title" content="{{ $titleText }}">
<meta property="og:description" content="{{ $desc }}">
<meta property="og:url" content="{{ url()->current() }}">
@if (filled($ogImage))
    <meta property="og:image" content="{{ $ogImage }}">
@endif
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $titleText }}">
<meta name="twitter:description" content="{{ $desc }}">
@if (filled($ogImage))
    <meta name="twitter:image" content="{{ $ogImage }}">
@endif
