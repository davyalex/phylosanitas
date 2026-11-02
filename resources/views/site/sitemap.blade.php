{!! '<' . '?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
    <url>
        <loc>{{ route('accueil') }}</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>{{ route('post.list') }}</loc>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    @foreach($categories as $cat)
        @if($cat->slug)
        <url>
            <loc>{{ route('post.list', ['category' => $cat->slug]) }}</loc>
            <changefreq>weekly</changefreq>
            <priority>0.7</priority>
        </url>
        @endif
    @endforeach
    @foreach($posts as $p)
        <url>
            <loc>{{ route('post.detail', ['slug' => $p->slug]) }}</loc>
            <lastmod>{{ \Carbon\Carbon::parse($p->updated_at ?? $p->created_at)->toAtomString() }}</lastmod>
            <changefreq>monthly</changefreq>
            <priority>0.6</priority>
            @if($img = $p->getFirstMediaUrl('image'))
            <image:image><image:loc>{{ url($img) }}</image:loc></image:image>
            @endif
        </url>
    @endforeach
    <url>
        <loc>{{ route('contact') }}</loc>
        <changefreq>yearly</changefreq>
        <priority>0.3</priority>
    </url>
</urlset>
