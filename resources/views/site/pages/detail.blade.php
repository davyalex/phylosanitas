@php
    $title     = $post->title ?: Str::limit(strip_tags($post->description ?? ''), 110);
    $plainText = trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($post->description ?? ''), ENT_QUOTES, 'UTF-8')));
    $metaDesc  = $post->meta_description ?: Str::limit($plainText, 158, '…');
    $wordCount = str_word_count($plainText);
    $readMin   = max(1, (int) round($wordCount / 200));
    $isPoll    = strtolower($post->category->title ?? '') === 'sondage';
    $shareUrl  = route('post.detail', ['slug' => $post->slug]);
    $imageUrl  = $post->getFirstMediaUrl('image') ? url($post->getFirstMediaUrl('image')) : asset('assets_site/img/logo/logo_tp.png');
    $related   = isset($post_last) ? $post_last->where('id', '!=', $post->id)->take(3) : collect();
@endphp

@extends('site.layout')
@section('title', Str::limit($title, 65, '…'))
@section('description', $metaDesc)
@section('image', $imageUrl)
@section('canonical', $shareUrl)
@section('og_type', 'article')

@push('meta')
    <meta property="article:published_time" content="{{ \Carbon\Carbon::parse($post->created_at)->toIso8601String() }}">
    <meta property="article:modified_time" content="{{ \Carbon\Carbon::parse($post->updated_at ?? $post->created_at)->toIso8601String() }}">
    @if($post->category)<meta property="article:section" content="{{ $post->category->title }}">@endif
@endpush

@push('jsonld')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@graph'   => array_values(array_filter([
        [
            '@type'            => $isPoll ? 'WebPage' : 'Article',
            '@id'              => $shareUrl . '#article',
            'mainEntityOfPage' => $shareUrl,
            'headline'         => Str::limit($title, 110, ''),
            'description'      => $metaDesc,
            'image'            => [$imageUrl],
            'datePublished'    => \Carbon\Carbon::parse($post->created_at)->toIso8601String(),
            'dateModified'     => \Carbon\Carbon::parse($post->updated_at ?? $post->created_at)->toIso8601String(),
            'inLanguage'       => 'fr-FR',
            'wordCount'        => $wordCount,
            'articleSection'   => $post->category->title ?? null,
            'author'           => ['@type' => 'Organization', 'name' => config('app.name'), 'url' => url('/')],
            'publisher'        => ['@id' => url('/') . '#organization'],
        ],
        [
            '@type'           => 'BreadcrumbList',
            'itemListElement' => array_values(array_filter([
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Accueil', 'item' => route('accueil')],
                $post->category ? ['@type' => 'ListItem', 'position' => 2, 'name' => $post->category->title, 'item' => route('post.list', ['category' => $post->category->slug])] : null,
                ['@type' => 'ListItem', 'position' => $post->category ? 3 : 2, 'name' => Str::limit($title, 80, '')],
            ])),
        ],
    ])),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@section('content')

<div id="reading-progress" class="reading-progress" aria-hidden="true"></div>

<div class="section section--tight">
    <div class="container-xl">

        <nav aria-label="Fil d'Ariane" class="crumbs">
            <a href="{{ route('accueil') }}">Accueil</a>
            @if($post->category)
                <i class="bi bi-chevron-right" aria-hidden="true"></i>
                <a href="{{ route('post.list', ['category' => $post->category->slug]) }}">{{ $post->category->title }}</a>
            @endif
            <i class="bi bi-chevron-right" aria-hidden="true"></i>
            <span aria-current="page">{{ Str::limit($title, 48) }}</span>
        </nav>

        <div class="layout layout--article">
            <div class="layout__main">

                <article class="article">
                    <header class="article__head">
                        <div class="article__tags">
                            @if($post->category)
                                <a href="{{ route('post.list', ['category' => $post->category->slug]) }}" class="tag">
                                    <x-cat-icon :cat="$post->category" />{{ $post->category->title }}
                                </a>
                            @endif
                            <span class="article__readtime"><i class="bi bi-clock" aria-hidden="true"></i>{{ $readMin }} min de lecture</span>
                        </div>

                        <h1 class="article__title">{{ $title }}</h1>

                        <div class="meta meta--lg">
                            <time datetime="{{ \Carbon\Carbon::parse($post->created_at)->toDateString() }}"><i class="bi bi-calendar3" aria-hidden="true"></i>{{ \Carbon\Carbon::parse($post->created_at)->translatedFormat('d F Y') }}</time>
                            <span><i class="bi bi-eye" aria-hidden="true"></i>{{ number_format($post->views_count ?? 0, 0, ',', ' ') }} vue{{ ($post->views_count ?? 0) > 1 ? 's' : '' }}</span>
                            <a href="#comments"><i class="bi bi-chat-left-text" aria-hidden="true"></i>{{ $post->commentaires_count }} commentaire{{ $post->commentaires_count > 1 ? 's' : '' }}</a>
                        </div>
                    </header>

                    @if($post->getFirstMedia('image'))
                        <figure class="article__figure">
                            <x-media-img :model="$post" size="large" :alt="$title" :eager="true"
                                         sizes="(min-width: 1200px) 800px, (min-width: 992px) 64vw, 100vw"
                                         width="1280" height="720" />
                        </figure>
                    @endif

                    @if($isPoll)
                        @include('site.pages.partials.poll')
                    @else
                        <div class="prose js-prose">
                            {!! $post->description !!}
                        </div>
                    @endif

                    {{-- Publicité intégrée (déplacée après le 3e paragraphe par site.js) --}}
                    <div class="js-inarticle-ad">
                        <x-ad-slot format="inarticle" />
                    </div>

                    @if($post->lien)
                        <a href="{{ $post->lien }}" target="_blank" rel="noopener noreferrer" class="source-link">
                            <i class="bi bi-link-45deg" aria-hidden="true"></i>
                            <span>Consulter la source officielle</span>
                            <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i>
                        </a>
                    @endif

                    @if($site['avertissement'] ?? null)
                        <aside class="notice" aria-label="Avertissement médical">
                            <i class="bi bi-info-circle" aria-hidden="true"></i>
                            <p>{{ $site['avertissement'] }}</p>
                        </aside>
                    @endif

                    <footer class="share">
                        <span class="share__label">Partager</span>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}" target="_blank" rel="noopener noreferrer" class="share__btn" aria-label="Partager sur Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="https://wa.me/?text={{ urlencode($title . ' ' . $shareUrl) }}" target="_blank" rel="noopener noreferrer" class="share__btn" aria-label="Partager sur WhatsApp"><i class="bi bi-whatsapp"></i></a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode($shareUrl) }}&text={{ urlencode(Str::limit($title, 80)) }}" target="_blank" rel="noopener noreferrer" class="share__btn" aria-label="Partager sur X (Twitter)"><i class="bi bi-twitter-x"></i></a>
                        <button type="button" class="share__btn js-copy-link" data-url="{{ $shareUrl }}" aria-label="Copier le lien">
                            <i class="bi bi-link-45deg" aria-hidden="true"></i>
                        </button>
                        <span class="share__feedback" role="status" aria-live="polite"></span>
                    </footer>
                </article>

                {{-- Fin de lecture --}}
                @include('site.pages.partials.end-read')

                {{-- Commentaires --}}
                <section class="panel" id="comments" aria-labelledby="comments-title">
                    <h2 id="comments-title" class="panel__title">
                        {{ $post->commentaires_count }} commentaire{{ $post->commentaires_count > 1 ? 's' : '' }}
                    </h2>

                    @forelse($post->commentaires as $item)
                        <div class="comment">
                            <span class="comment__avatar" aria-hidden="true">{{ strtoupper(mb_substr($item->user_name ?? 'A', 0, 1)) }}</span>
                            <div class="comment__body">
                                <div class="comment__head">
                                    <strong>{{ $item->user_name ?? 'Anonyme' }}</strong>
                                    <time datetime="{{ \Carbon\Carbon::parse($item->created_at)->toIso8601String() }}">{{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}</time>
                                </div>
                                <p>{{ $item->message }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="muted">Aucun commentaire pour le moment. Soyez le premier à réagir.</p>
                    @endforelse

                    <div class="comment-form" id="comment-form-section">
                        <h3 class="comment-form__title">Laisser un commentaire</h3>

                        @if($errors->any())
                            <div class="alert-box" role="alert">
                                <i class="bi bi-exclamation-triangle" aria-hidden="true"></i>
                                <div>@foreach($errors->all() as $error)<span>{{ $error }}</span>@endforeach</div>
                            </div>
                        @endif

                        <form action="{{ route('post.comment') }}" method="POST" id="comment-form">
                            @csrf
                            <x-honeypot />
                            <input type="hidden" name="post_id" value="{{ $post->id }}">
                            @guest
                                <div class="field">
                                    <label for="c-name">Votre nom <span class="req" aria-hidden="true">*</span></label>
                                    <input id="c-name" type="text" name="name" class="@error('name') is-invalid @enderror" value="{{ old('name') }}" required autocomplete="name">
                                    @error('name')<p class="field__error">{{ $message }}</p>@enderror
                                </div>
                            @endguest
                            <div class="field">
                                <label for="c-message">Votre message <span class="req" aria-hidden="true">*</span></label>
                                <textarea id="c-message" name="message" rows="5" class="@error('message') is-invalid @enderror" placeholder="Partagez votre avis ou votre question…" required>{{ old('message') }}</textarea>
                                @error('message')<p class="field__error">{{ $message }}</p>@enderror
                            </div>
                            <button type="submit" class="btn btn-medical" id="comment-submit">Publier le commentaire</button>
                        </form>
                    </div>
                </section>

                {{-- À lire aussi --}}
                @if($related->count())
                    <section class="related" aria-labelledby="related-title">
                        <div class="sec-head">
                            <h2 id="related-title" class="sec-head__title">À lire aussi</h2>
                        </div>
                        <div class="card-grid card-grid--3">
                            @foreach($related as $rel)
                                <x-post-card :post="$rel" />
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>

            <aside class="layout__side" aria-label="Compléments">
                @include('site.pages.sections.sidebar')
            </aside>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if($errors->any())
            document.getElementById('comment-form-section').scrollIntoView({ behavior: 'smooth', block: 'start' });
        @endif
        var form = document.getElementById('comment-form');
        if (form) form.addEventListener('submit', function () {
            var btn = document.getElementById('comment-submit');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Envoi…';
        });
    });
</script>
@endpush

@endsection
