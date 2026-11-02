@php
    $listName = $category_req ? $category_req->title : 'Tous les articles santé';
    $pageNum  = $post->currentPage();
@endphp
@extends('site.layout')
@section('title', $listName . ($pageNum > 1 ? " — page $pageNum" : ''))
@section('description', $category_req
    ? "Articles et conseils santé sur le thème « {$category_req->title} » : informations fiables et accessibles, rédigées par des professionnels de santé."
    : "Tous les articles santé de " . config('app.name') . " : actualités médicales, prévention, nutrition, conseils bien-être et sondages.")
@section('canonical', $post->url($pageNum) === $post->url(1) ? route('post.list', array_filter(['category' => $category_req->slug ?? null])) : $post->url($pageNum))

@push('meta')
    @if($post->previousPageUrl())<link rel="prev" href="{{ $post->previousPageUrl() }}">@endif
    @if($post->nextPageUrl())<link rel="next" href="{{ $post->nextPageUrl() }}">@endif
@endpush

@push('jsonld')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type'    => 'BreadcrumbList',
    'itemListElement' => array_values(array_filter([
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Accueil', 'item' => route('accueil')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Articles', 'item' => route('post.list')],
        $category_req ? ['@type' => 'ListItem', 'position' => 3, 'name' => $category_req->title, 'item' => route('post.list', ['category' => $category_req->slug])] : null,
    ])),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@section('content')
<section class="section section--tight">
    <div class="container-xl">

        <nav aria-label="Fil d'Ariane" class="crumbs">
            <a href="{{ route('accueil') }}">Accueil</a>
            <i class="bi bi-chevron-right" aria-hidden="true"></i>
            @if($category_req)
                <a href="{{ route('post.list') }}">Articles</a>
                <i class="bi bi-chevron-right" aria-hidden="true"></i>
                <span aria-current="page">{{ $category_req->title }}</span>
            @else
                <span aria-current="page">Articles</span>
            @endif
        </nav>

        <header class="page-head">
            <span class="page-head__icon" aria-hidden="true">
                @if($category_req)<x-cat-icon :cat="$category_req" />@else<i class="bi bi-collection"></i>@endif
            </span>
            <div>
                <h1 class="page-head__title">{{ $category_req ? $category_req->title : 'Tous les articles' }}</h1>
                <p class="page-head__sub">{{ $post->total() }} article{{ $post->total() > 1 ? 's' : '' }} publié{{ $post->total() > 1 ? 's' : '' }}</p>
            </div>
        </header>

        <div class="layout">
            <div class="layout__main">
                @if($post->isEmpty())
                    <div class="empty">
                        <i class="bi bi-journal-x" aria-hidden="true"></i>
                        <h2>Aucun article dans cette catégorie</h2>
                        <p>Revenez bientôt, ou explorez une autre thématique.</p>
                        <a href="{{ route('accueil') }}" class="btn btn-medical">Retour à l'accueil</a>
                    </div>
                @else
                    <div class="post-list">
                        @foreach($post as $item)
                            @php
                                $title = $item->title ?: Str::limit(strip_tags($item->description ?? ''), 110);
                                $url   = route('post.detail', ['slug' => $item->slug]);
                            @endphp
                            <article class="litem">
                                <div class="litem__media">
                                    <x-media-img :model="$item" :alt="$title" sizes="(min-width: 576px) 260px, 112px" />
                                    @if($item->lien)
                                        <span class="litem__ext" title="Source externe"><i class="bi bi-box-arrow-up-right" aria-hidden="true"></i><span class="visually-hidden">Source externe</span></span>
                                    @endif
                                </div>
                                <div class="litem__body">
                                    @if($item->category)
                                        <span class="litem__cat"><x-cat-icon :cat="$item->category" />{{ $item->category->title }}</span>
                                    @endif
                                    <h2 class="litem__title"><a href="{{ $url }}" class="litem__link">{{ $title }}</a></h2>
                                    <div class="meta">
                                        <time datetime="{{ \Carbon\Carbon::parse($item->created_at)->toDateString() }}"><i class="bi bi-calendar3" aria-hidden="true"></i>{{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}</time>
                                        <span><i class="bi bi-eye" aria-hidden="true"></i>{{ number_format($item->views_count ?? 0, 0, ',', ' ') }} vue{{ ($item->views_count ?? 0) > 1 ? 's' : '' }}</span>
                                        @if(($item->commentaires_count ?? 0) > 0)
                                            <span><i class="bi bi-chat-left-text" aria-hidden="true"></i>{{ $item->commentaires_count }}</span>
                                        @endif
                                    </div>
                                    <span class="litem__read" aria-hidden="true">Lire l'article <i class="bi bi-arrow-right"></i></span>
                                </div>
                            </article>
                            @if($loop->iteration === 5 && $post->count() > 6)
                                <x-ad-slot format="infeed" />
                            @endif
                        @endforeach
                    </div>

                    <div class="pager-wrap">
                        {!! $post->appends(request()->query())->links('vendor.pagination.custom') !!}
                    </div>
                @endif
            </div>

            <aside class="layout__side" aria-label="Compléments">
                @include('site.pages.sections.sidebar')
            </aside>
        </div>
    </div>
</section>
@endsection
