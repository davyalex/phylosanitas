@extends('site.layout')
@section('title', $category_req ? Str::title($category_req->title) : 'Articles')
@section('description', 'Parcourez les articles de la catégorie ' . ($category_req ? $category_req->title : 'santé') . ' sur PhyloSanitas.')

@section('content')
<section class="category-list-section py-5 mt-4">
    <div class="container-xl">
        <div class="row g-4">

            {{-- Colonne principale --}}
            <div class="col-lg-9" data-aos="fade-up">

                {{-- En-tête catégorie --}}
                <div class="cat-list-header">
                    <a href="javascript:history.go(-1)" class="cat-list-back">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div class="cat-list-info">
                        @if($category_req)
                            @php
                                $ico = match(true) {
                                    str_contains(strtolower($category_req->slug),'actualite') => 'newspaper',
                                    str_contains(strtolower($category_req->slug),'sondage')   => 'bar-chart-fill',
                                    str_contains(strtolower($category_req->title),'nutri')    => 'egg-fried',
                                    str_contains(strtolower($category_req->title),'sport')    => 'heart-pulse-fill',
                                    str_contains(strtolower($category_req->title),'obés')     => 'person-arms-up',
                                    str_contains(strtolower($category_req->title),'scolaire') => 'mortarboard-fill',
                                    default => 'collection-fill',
                                };
                            @endphp
                            <span class="cat-list-icon"><i class="bi bi-{{ $ico }}"></i></span>
                            <h1 class="cat-list-title">{{ Str::title($category_req->title) }}</h1>
                        @else
                            <span class="cat-list-icon"><i class="bi bi-newspaper"></i></span>
                            <h1 class="cat-list-title">Tous les articles</h1>
                        @endif
                    </div>
                    <span class="cat-list-count">{{ $post->total() }} article{{ $post->total() > 1 ? 's' : '' }}</span>
                </div>

                {{-- Liste articles --}}
                @if($post->isEmpty())
                    <div class="empty-state text-center py-5">
                        <i class="bi bi-journal-x empty-state-icon"></i>
                        <h5 class="mt-3">Aucun article dans cette catégorie.</h5>
                        <a href="{{ route('accueil') }}" class="btn btn-medical mt-3">
                            <i class="bi bi-house-fill me-2"></i>Retour à l'accueil
                        </a>
                    </div>
                @else
                    <div class="post-list mt-4">
                        @foreach($post as $item)
                            @php
                                $isSondage = strtolower($item->category->title ?? '') === 'sondage';
                                $rawTitle  = $item->title ?? strip_tags($item->description ?? '');
                                $title     = Str::title(Str::limit($rawTitle, 80));
                                $excerpt   = Str::limit(strip_tags($item->description ?? ''), 120);
                                $wordCount = str_word_count(strip_tags($item->description ?? ''));
                                $readMin   = max(1, round($wordCount / 200));
                                $catSlug   = strtolower($item->category->slug ?? '');
                                $catIcon   = match(true) {
                                    str_contains($catSlug,'actualite') => 'newspaper',
                                    str_contains($catSlug,'sondage')   => 'bar-chart-fill',
                                    str_contains(strtolower($item->category->title ?? ''),'sport') => 'heart-pulse-fill',
                                    str_contains(strtolower($item->category->title ?? ''),'obés')  => 'person-arms-up',
                                    str_contains(strtolower($item->category->title ?? ''),'scolaire') => 'mortarboard-fill',
                                    default => 'file-medical-fill',
                                };
                            @endphp
                            <article class="post-list-item">
                                <a href="{{ route('post.detail', ['slug' => $item->slug]) }}"
                                   class="post-list-thumb">
                                    <img src="{{ $item->getFirstMediaUrl('image') ?: asset('assets_site/img/medc.jpg') }}"
                                         loading="lazy"
                                         alt="{{ $rawTitle }}">
                                    @if($item->lien)
                                        <span class="post-list-ext" title="Lien externe">
                                            <i class="bi bi-box-arrow-up-right"></i>
                                        </span>
                                    @endif
                                </a>
                                <div class="post-list-body">
                                    <span class="post-list-cat">
                                        <i class="bi bi-{{ $catIcon }} me-1"></i>
                                        {{ $item->category->title ?? '' }}
                                    </span>
                                    <h2 class="post-list-title">
                                        <a href="{{ route('post.detail', ['slug' => $item->slug]) }}">
                                            {{ $title }}
                                        </a>
                                    </h2>
                                    @if($excerpt)
                                        <p class="post-list-excerpt">{{ $excerpt }}</p>
                                    @endif
                                    <div class="post-list-meta">
                                        <span><i class="bi bi-calendar3"></i> {{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}</span>
                                        <span><i class="bi bi-chat-left-quote"></i> {{ $item->commentaires_count ?? 0 }} commentaire{{ ($item->commentaires_count ?? 0) > 1 ? 's' : '' }}</span>
                                        <span><i class="bi bi-clock"></i> {{ $readMin }} min de lecture</span>
                                    </div>
                                    <a href="{{ route('post.detail', ['slug' => $item->slug]) }}"
                                       class="post-list-read">
                                        Lire l'article <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="d-flex justify-content-center mt-5">
                        {!! $post->appends(request()->query())->links('vendor.pagination.custom') !!}
                    </div>
                @endif

            </div>{{-- /col-lg-9 --}}

            {{-- Sidebar --}}
            <div class="col-lg-3">
                <div class="sidebar-wrapper sticky-sidebar">
                    @include('site.pages.sections.sidebar')
                </div>
            </div>

        </div>
    </div>
</section>
@endsection
