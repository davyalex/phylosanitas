@extends('site.layout')
@section('title', 'Accueil')
@section('description', 'PhyloSanitas — Votre référence santé en Côte d\'Ivoire. Articles médicaux, actualités santé, sondages et conseils bien-être.')

@section('content')

{{-- ① Hero Slider --}}
@include('site.pages.sections.slider')

{{-- ② Breaking News Ticker --}}
@if($post_last->count())
<div class="breaking-news-bar">
    <div class="container-xl">
        <div class="d-flex align-items-center gap-0">
            <div class="breaking-label flex-shrink-0">
                <i class="bi bi-lightning-charge-fill me-1"></i>À la une
            </div>
            <div class="ticker-track-wrapper">
                <div class="ticker-track" id="newsTicker">
                    @foreach($post_last as $item)
                        <a href="{{ route('post.detail', ['slug' => $item->slug]) }}"
                           class="ticker-item">
                            <i class="bi bi-dot"></i>
                            {{ Str::limit($item->title ?? strip_tags($item->description), 70) }}
                        </a>
                    @endforeach
                    {{-- Duplicate pour boucle infinie --}}
                    @foreach($post_last as $item)
                        <a href="{{ route('post.detail', ['slug' => $item->slug]) }}"
                           class="ticker-item" aria-hidden="true">
                            <i class="bi bi-dot"></i>
                            {{ Str::limit($item->title ?? strip_tags($item->description), 70) }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- ③ Articles Vedettes (Magazine Layout) --}}
@if($post->count() >= 1)
<section class="featured-section py-5">
    <div class="container-xl">

        <div class="section-header">
            <h2 class="section-title">
                <span class="section-accent"></span>Dernières publications
            </h2>
            <a href="{{ route('post.list') }}" class="section-link">
                Voir tout <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row g-4">

            {{-- Article vedette principal --}}
            @php $featuredPost = $post->first() @endphp
            <div class="col-lg-7">
                <a href="{{ route('post.detail', ['slug' => $featuredPost->slug]) }}"
                   class="featured-main-card d-block text-decoration-none">
                    <div class="featured-main-img-wrap">
                        <img src="{{ $featuredPost->getFirstMediaUrl('image') ?: asset('assets_site/img/medc.jpg') }}"
                             alt="{{ $featuredPost->title }}"
                             loading="eager">
                        <div class="featured-main-overlay">
                            <span class="featured-category-badge">
                                {{ $featuredPost->category->title ?? '' }}
                            </span>
                            <h2 class="featured-main-title">
                                {{ Str::limit($featuredPost->title ?? strip_tags($featuredPost->description), 90) }}
                            </h2>
                            <div class="featured-main-meta">
                                <span><i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($featuredPost->created_at)->diffForHumans() }}</span>
                                <span><i class="bi bi-chat-left-quote me-1"></i>{{ $featuredPost->commentaires_count ?? 0 }} commentaire(s)</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            {{-- Articles secondaires (2 à droite) --}}
            <div class="col-lg-5">
                <div class="d-flex flex-column gap-3 h-100">
                    @foreach($post->skip(1)->take(2) as $item)
                        <a href="{{ route('post.detail', ['slug' => $item->slug]) }}"
                           class="featured-side-card d-flex text-decoration-none flex-grow-1">
                            <div class="featured-side-img-wrap flex-shrink-0">
                                <img src="{{ $item->getFirstMediaUrl('image') ?: asset('assets_site/img/medc.jpg') }}"
                                     alt="{{ $item->title }}"
                                     loading="lazy">
                            </div>
                            <div class="featured-side-body">
                                <span class="featured-side-badge">{{ $item->category->title ?? '' }}</span>
                                <h5 class="featured-side-title">
                                    {{ Str::limit($item->title ?? strip_tags($item->description), 75) }}
                                </h5>
                                <span class="featured-side-date">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    {{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</section>
@endif

{{-- ④ Bande Catégories --}}
@if($category->filter(fn($c) => !in_array(strtolower($c->slug), ['sondage', 'actualites']))->count())
<section class="categories-strip-section">
    <div class="container-xl">
        <div class="categories-scroll-track">
            <span class="categories-label">
                <i class="bi bi-collection-fill"></i> Thématiques
            </span>
            @foreach($category->filter(fn($c) => !in_array(strtolower($c->slug), ['sondage', 'actualites'])) as $cat)
                @php
                    $t = strtolower($cat->title);
                    $icon = match(true) {
                        str_contains($t, 'public')                              => 'shield-plus-fill',
                        str_contains($t, 'obés') || str_contains($t, 'obes')   => 'person-arms-up',
                        str_contains($t, 'thès') || str_contains($t, 'thes')   => 'journal-medical',
                        str_contains($t, 'scolaire') || str_contains($t, 'etude') => 'mortarboard-fill',
                        str_contains($t, 'nutrition') || str_contains($t, 'aliment') => 'egg-fried',
                        str_contains($t, 'sport') || str_contains($t, 'fitness') => 'heart-pulse-fill',
                        default                                                  => 'file-medical-fill',
                    };
                @endphp
                <a href="{{ route('post.list', ['category' => $cat->slug]) }}"
                   class="cat-pill {{ request('category') === $cat->slug ? 'cat-pill--active' : '' }}">
                    <i class="bi bi-{{ $icon }}"></i>
                    {{ $cat->title }}
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ⑤ Articles récents + Sidebar --}}
{{-- On passe tous les articles : si ≤ 3 articles, la grille n'est pas vide --}}
@include('site.pages.sections.post', ['post' => $post])

@endsection
