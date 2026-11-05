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
    {{-- Services santé --}}
    @foreach(array_keys(config('sante.services', [])) as $r)
        <url>
            <loc>{{ route($r) }}</loc>
            <changefreq>{{ $r === 'garde' ? 'daily' : 'weekly' }}</changefreq>
            <priority>{{ $r === 'garde' ? '0.9' : '0.7' }}</priority>
        </url>
    @endforeach
    @foreach($services['outils'] ?? [] as $o)
        <url><loc>{{ route('outils.show', $o) }}</loc><changefreq>monthly</changefreq><priority>0.6</priority></url>
    @endforeach
    @foreach([
        'quiz.jouer'      => $services['quiz'] ?? [],
        'auteur.show'     => $services['auteurs'] ?? [],
    ] as $r => $items)
        @foreach($items as $it)
            <url>
                <loc>{{ route($r, $it->slug) }}</loc>
                @if($it->updated_at)<lastmod>{{ \Carbon\Carbon::parse($it->updated_at)->toAtomString() }}</lastmod>@endif
                <changefreq>monthly</changefreq>
                <priority>{{ $r === 'auteur.show' ? '0.4' : '0.6' }}</priority>
            </url>
        @endforeach
    @endforeach
    <url>
        <loc>{{ route('contact') }}</loc>
        <changefreq>yearly</changefreq>
        <priority>0.3</priority>
    </url>
    <url>
        <loc>{{ route('privacy') }}</loc>
        <changefreq>yearly</changefreq>
        <priority>0.2</priority>
    </url>
</urlset>
