{{-- Carte d'un dossier : mosaïque des images de ses premiers articles. Variable : $d (Dossier, avec postsPublies chargés) --}}
@php
    $imgs = ($d->relationLoaded('postsPublies') ? $d->postsPublies : collect())->take(3);
@endphp
<article class="dcard">
    <div class="dcard__mosaic dcard__mosaic--{{ max(1, $imgs->count()) }}" aria-hidden="true">
        @forelse($imgs as $p)
            <x-media-img :model="$p" alt="" sizes="(min-width: 1200px) 260px, (min-width: 768px) 45vw, 90vw" />
        @empty
            <img src="{{ asset('assets_site/img/medc.jpg') }}" alt="" loading="lazy">
        @endforelse
    </div>
    <div class="dcard__body">
        <span class="dcard__count"><i class="bi bi-list-ol" aria-hidden="true"></i>{{ $d->articles_count }} article{{ $d->articles_count > 1 ? 's' : '' }}</span>
        <h2 class="dcard__title"><a href="{{ route('dossiers.show', $d->slug) }}">{{ $d->titre }}</a></h2>
        @if($d->intro)<p>{{ Str::limit($d->intro, 140) }}</p>@endif
    </div>
</article>
