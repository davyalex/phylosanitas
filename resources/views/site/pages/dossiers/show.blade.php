@php
    $url = route('dossiers.show', $dossier->slug);
    $readTotal = $articles->sum(fn($a) => max(1, (int) round(str_word_count(strip_tags($a->description ?? '')) / 200)));
@endphp

@extends('site.layout')
@section('title', $dossier->titre)
@section('description', $dossier->intro ? Str::limit($dossier->intro, 155, '…') : 'Dossier santé : ' . $articles->count() . ' articles pour tout comprendre sur « ' . $dossier->titre . ' ».')
@section('canonical', $url)
@if($articles->first()?->getFirstMediaUrl('image'))
    @section('image', url($articles->first()->getFirstMediaUrl('image')))
@endif

@push('jsonld')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type'    => 'ItemList',
    'name'     => $dossier->titre,
    'url'      => $url,
    'itemListOrder'   => 'https://schema.org/ItemListOrderAscending',
    'numberOfItems'   => $articles->count(),
    'itemListElement' => $articles->values()->map(fn($a, $i) => [
        '@type' => 'ListItem', 'position' => $i + 1, 'name' => $a->title, 'url' => route('post.detail', ['slug' => $a->slug]),
    ])->all(),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@section('content')
<x-svc-hero icon="collection-play" :titre="$dossier->titre" :parent="['Dossiers' => route('dossiers.index')]">
    {{ $dossier->intro }}
    <x-slot:extra>
        <div class="svc-hero__meta">
            <span><i class="bi bi-list-ol" aria-hidden="true"></i>{{ $articles->count() }} articles</span>
            <span><i class="bi bi-clock" aria-hidden="true"></i>{{ $readTotal }} min de lecture au total</span>
        </div>
        @if($articles->first())
            <a href="{{ route('post.detail', ['slug' => $articles->first()->slug]) }}" class="btn btn-hero mt-3">Commencer la lecture <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
        @endif
    </x-slot:extra>
</x-svc-hero>

<section class="svc-body">
    <div class="container-xl">
        <div class="layout">
            <div class="layout__main">
                <ol class="dpath">
                    @foreach($articles as $a)
                        <li class="dpath__item">
                            <span class="dpath__num" aria-hidden="true">{{ $loop->iteration }}</span>
                            <article class="dpath__card">
                                <div class="dpath__media"><x-media-img :model="$a" :alt="$a->title" sizes="140px" /></div>
                                <div class="dpath__body">
                                    @if($a->category)<span class="litem__cat"><x-cat-icon :cat="$a->category" />{{ $a->category->title }}</span>@endif
                                    <h2 class="dpath__title"><a href="{{ route('post.detail', ['slug' => $a->slug]) }}"><span class="visually-hidden">Étape {{ $loop->iteration }} : </span>{{ $a->title }}</a></h2>
                                    <p>{{ $a->meta_description ?: Str::limit(trim(html_entity_decode(strip_tags($a->description ?? ''))), 160) }}</p>
                                </div>
                            </article>
                        </li>
                    @endforeach
                </ol>

                <footer class="share mt-4" style="border:1px solid var(--line);border-radius:var(--r-lg)">
                    <span class="share__label">Partager ce dossier</span>
                    <a href="https://wa.me/?text={{ urlencode($dossier->titre . ' ' . $url) }}" target="_blank" rel="noopener noreferrer" class="share__btn" aria-label="Partager sur WhatsApp"><i class="bi bi-whatsapp"></i></a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($url) }}" target="_blank" rel="noopener noreferrer" class="share__btn" aria-label="Partager sur Facebook"><i class="bi bi-facebook"></i></a>
                    <button type="button" class="share__btn js-copy-link" data-url="{{ $url }}" aria-label="Copier le lien"><i class="bi bi-link-45deg" aria-hidden="true"></i></button>
                    <span class="share__feedback" role="status" aria-live="polite"></span>
                </footer>

                @if($autres->count())
                    <div class="mini-head"><h2>Autres dossiers</h2><a href="{{ route('dossiers.index') }}" class="sec-head__link">Tous les dossiers <i class="bi bi-arrow-right" aria-hidden="true"></i></a></div>
                    <div class="chips-wrap">
                        @foreach($autres as $d)
                            <a href="{{ route('dossiers.show', $d->slug) }}" class="chip"><i class="bi bi-collection-play" aria-hidden="true"></i>{{ $d->titre }}<span class="chip__count">{{ $d->articles_count }}</span></a>
                        @endforeach
                    </div>
                @endif
            </div>

            <aside class="layout__side" aria-label="Compléments">
                @include('site.pages.sections.sidebar', ['showRecent' => false])
            </aside>
        </div>
    </div>
</section>
@endsection
