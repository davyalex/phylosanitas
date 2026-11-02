@extends('site.layout')
@section('title', 'Accueil')
@section('description', 'PhyloSanitas — Votre référence santé en Côte d\'Ivoire. Articles médicaux, actualités santé, sondages et conseils bien-être.')

@section('content')

{{-- ① Hero (carrousel admin ou bannière par défaut) --}}
@include('site.pages.sections.slider')

{{-- ② Thématiques --}}
@php
    $topics = $category->filter(fn($c) => !in_array(strtolower($c->slug), ['sondage', 'actualites']));
@endphp
@if($topics->count())
<nav class="topics" aria-label="Thématiques santé">
    <div class="container-xl">
        <div class="topics__track">
            <span class="topics__label">Thématiques</span>
            @foreach($topics as $cat)
                <a href="{{ route('post.list', ['category' => $cat->slug]) }}" class="chip">
                    <x-cat-icon :cat="$cat" />{{ $cat->title }}
                    @if($cat->posts_count)<span class="chip__count">{{ $cat->posts_count }}</span>@endif
                </a>
            @endforeach
        </div>
    </div>
</nav>
@endif

{{-- ③ À la une : 1 article principal + 3 secondaires --}}
@if($post->count())
@php
    $lead      = $post->first();
    $leadTitle = $lead->title ?: Str::limit(strip_tags($lead->description ?? ''), 110);
    $sidePosts = $post->slice(1, 3);
    $gridPosts = $post->slice(4, 6); // 6 = 2 rangées complètes en 3 colonnes / 3 en 2 colonnes
@endphp
<section class="section section--white" aria-labelledby="une-title">
    <div class="container-xl">
        <div class="sec-head">
            <h2 id="une-title" class="sec-head__title">À la une</h2>
            <a href="{{ route('post.list') }}" class="sec-head__link">Tous les articles <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
        </div>

        <div class="feature">
            <article class="lead">
                <div class="lead__media">
                    <x-media-img :model="$lead" size="large" :alt="$leadTitle" :eager="true"
                                 sizes="(min-width: 1200px) 760px, (min-width: 992px) 60vw, 100vw"
                                 width="1280" height="800" />
                </div>
                <div class="lead__body">
                    @if($lead->category)
                        <span class="tag tag--light"><x-cat-icon :cat="$lead->category" />{{ $lead->category->title }}</span>
                    @endif
                    <h3 class="lead__title">
                        <a href="{{ route('post.detail', ['slug' => $lead->slug]) }}" class="lead__link">{{ $leadTitle }}</a>
                    </h3>
                    <div class="meta meta--light">
                        <time datetime="{{ \Carbon\Carbon::parse($lead->created_at)->toDateString() }}"><i class="bi bi-calendar3" aria-hidden="true"></i>{{ \Carbon\Carbon::parse($lead->created_at)->diffForHumans() }}</time>
                        <span><i class="bi bi-eye" aria-hidden="true"></i>{{ number_format($lead->views_count ?? 0, 0, ',', ' ') }} vue{{ ($lead->views_count ?? 0) > 1 ? 's' : '' }}</span>
                        @if(($lead->commentaires_count ?? 0) > 0)
                            <span><i class="bi bi-chat-left-text" aria-hidden="true"></i>{{ $lead->commentaires_count }}</span>
                        @endif
                    </div>
                </div>
            </article>

            @if($sidePosts->count())
                <div class="feature__side">
                    @foreach($sidePosts as $item)
                        <x-post-row :post="$item" heading="h3" class="prow--feature" />
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>

{{-- ④ Bandeau publicitaire --}}
<div class="container-xl">
    <x-ad-slot format="leaderboard" class="ad--spaced" />
</div>
@endif

{{-- ⑤ Prévention : bons réflexes illustrés --}}
@include('site.pages.sections.prevention')

{{-- ⑥ Articles récents + newsletter + « Quand consulter ? » + Sidebar --}}
@include('site.pages.sections.post', ['gridPosts' => $gridPosts ?? collect()])

@endsection
