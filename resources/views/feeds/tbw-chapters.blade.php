{!! '<'.'?xml version="1.0" encoding="UTF-8"?>' !!}
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>{{ config('app.name') }} — The Book With No Name (new chapters)</title>
        <link>{{ url('/') }}</link>
        <description>Latest published manuscript chapters for The Book With No Name.</description>
        <language>{{ str_replace('_', '-', app()->getLocale()) }}</language>
        <atom:link href="{{ route('feed.chapters') }}" rel="self" type="application/rss+xml"/>
        @foreach($chapters as $ch)
            <item>
                <title>{{ $ch->readerHeadingLine() }}</title>
                <link>{{ route('chapters.show', $ch) }}</link>
                <guid isPermaLink="true">{{ route('chapters.show', $ch) }}</guid>
                <pubDate>{{ ($ch->published_at ?? $ch->created_at)->format('r') }}</pubDate>
                <description><![CDATA[{{ Str::limit(strip_tags($ch->content), 400) }}]]></description>
            </item>
        @endforeach
    </channel>
</rss>
