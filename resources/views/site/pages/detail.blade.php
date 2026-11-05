@php
    $title     = $post->title ?: Str::limit(strip_tags($post->description ?? ''), 110);
    $plainText = trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($post->description ?? ''), ENT_QUOTES, 'UTF-8')));
    $metaDesc  = $post->meta_description ?: Str::limit($plainText, 158, '…');
    $wordCount = str_word_count($plainText);
    $readMin   = max(1, (int) round($wordCount / 200));
    $isPoll    = strtolower($post->category->title ?? '') === 'sondage';
    $shareUrl  = route('post.detail', ['slug' => $post->slug]);
    $imageUrl  = $post->getFirstMediaUrl('image') ? url($post->getFirstMediaUrl('image')) : asset('assets_site/img/logo/logo_tp.png');
    $related   = (isset($related) && $related->count()) ? $related : (isset($post_last) ? $post_last->where('id', '!=', $post->id)->take(3) : collect());
    // Auteur et relecteur affichés seulement si leur profil public est activé (Admin > Mon profil > Profil public)
    $auteur    = ($post->user && $post->user->profil_public) ? $post->user : null;
    $relecteur = ($post->reviewer && $post->reviewer->profil_public) ? $post->reviewer : null;
    $majLe     = \Carbon\Carbon::parse($post->updated_at ?? $post->created_at);
    $sources   = $post->listeSources();
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
        array_filter([
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
            'author'           => $auteur
                ? array_filter(['@type' => 'Person', 'name' => $auteur->nom_public, 'jobTitle' => $auteur->titre_pro, 'url' => route('auteur.show', $auteur->slug)])
                : ['@type' => 'Organization', 'name' => config('app.name'), 'url' => url('/')],
            'reviewedBy'       => $relecteur ? array_filter(['@type' => 'Person', 'name' => $relecteur->nom_public, 'jobTitle' => $relecteur->titre_pro, 'url' => route('auteur.show', $relecteur->slug)]) : null,
            'citation'         => $sources ? array_map(fn($s) => $s['url'] ?: $s['texte'], $sources) : null,
            'publisher'        => ['@id' => url('/') . '#organization'],
        ], fn($v) => $v !== null),
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

                        @if($auteur || $relecteur)
                            <div class="byline">
                                @if($auteur)
                                    <div class="byline__person">
                                        <span class="avatar">@if($auteur->photoUrl())<img src="{{ $auteur->photoUrl() }}" alt="" loading="lazy">@else{{ $auteur->initiales() }}@endif</span>
                                        <span>Par <a href="{{ route('auteur.show', $auteur->slug) }}" rel="author">{{ $auteur->nom_public }}</a>@if($auteur->titre_pro)<small>{{ $auteur->titre_pro }}</small>@endif</span>
                                    </div>
                                @endif
                                @if($relecteur && (!$auteur || $relecteur->id !== $auteur->id))
                                    <span class="byline__reviewed"><i class="bi bi-patch-check-fill" aria-hidden="true"></i>Relu par&nbsp;<a href="{{ route('auteur.show', $relecteur->slug) }}">{{ $relecteur->nom_public }}</a>@if($relecteur->titre_pro), {{ Str::lcfirst($relecteur->titre_pro) }}@endif</span>
                                @elseif($relecteur)
                                    <span class="byline__reviewed"><i class="bi bi-patch-check-fill" aria-hidden="true"></i>Rédigé par un professionnel de santé</span>
                                @endif
                            </div>
                        @endif

                        <div class="meta meta--lg">
                            <time datetime="{{ \Carbon\Carbon::parse($post->created_at)->toDateString() }}"><i class="bi bi-calendar3" aria-hidden="true"></i>{{ \Carbon\Carbon::parse($post->created_at)->translatedFormat('d F Y') }}</time>
                            @if($majLe->gt(\Carbon\Carbon::parse($post->created_at)->addDay()))
                                <span class="updated"><i class="bi bi-arrow-repeat" aria-hidden="true"></i>Mis à jour le {{ $majLe->translatedFormat('d F Y') }}</span>
                            @endif
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

                    @if(count($sources))
                        <section class="sources" aria-labelledby="sources-title">
                            <h2 id="sources-title"><i class="bi bi-journal-bookmark" aria-hidden="true"></i>Sources</h2>
                            <ol>
                                @foreach($sources as $src)
                                    <li>@if($src['url'])<a href="{{ $src['url'] }}" target="_blank" rel="noopener nofollow">{{ $src['texte'] }}</a>@else{{ $src['texte'] }}@endif</li>
                                @endforeach
                            </ol>
                        </section>
                    @endif

                    @if($dossierNav ?? null)
                        <nav class="dnav" aria-label="Dossier : {{ $dossierNav->dossier->titre }}">
                            <div class="dnav__head">
                                <span><i class="bi bi-collection-play" aria-hidden="true"></i> Dossier : <strong>{{ $dossierNav->dossier->titre }}</strong> · article {{ $dossierNav->rang }} sur {{ $dossierNav->total }}</span>
                                <a href="{{ route('dossiers.show', $dossierNav->dossier->slug) }}">Voir tout le dossier</a>
                            </div>
                            <div class="dnav__progress" aria-hidden="true">@for($k = 1; $k <= $dossierNav->total; $k++)<span class="{{ $k <= $dossierNav->rang ? 'is-done' : '' }}"></span>@endfor</div>
                            <div class="dnav__links">
                                @if($dossierNav->prev)
                                    <a href="{{ route('post.detail', ['slug' => $dossierNav->prev->slug]) }}" class="dnav__link"><small><i class="bi bi-arrow-left" aria-hidden="true"></i> Précédent</small>{{ Str::limit($dossierNav->prev->title, 80) }}</a>
                                @endif
                                @if($dossierNav->next)
                                    <a href="{{ route('post.detail', ['slug' => $dossierNav->next->slug]) }}" class="dnav__link dnav__link--next"><small>Suivant <i class="bi bi-arrow-right" aria-hidden="true"></i></small>{{ Str::limit($dossierNav->next->title, 80) }}</a>
                                @endif
                            </div>
                        </nav>
                    @endif

                    @if($site['avertissement'] ?? null)
                        <aside class="notice" aria-label="Avertissement médical">
                            <i class="bi bi-info-circle" aria-hidden="true"></i>
                            <p>{{ $site['avertissement'] }}</p>
                        </aside>
                    @endif

                    @if($auteur && $auteur->bio)
                        <aside class="author-box" aria-label="À propos de l'auteur">
                            <span class="avatar avatar--md">@if($auteur->photoUrl())<img src="{{ $auteur->photoUrl() }}" alt="" loading="lazy">@else{{ $auteur->initiales() }}@endif</span>
                            <div>
                                <span class="author-box__label">L'auteur</span>
                                <h2><a href="{{ route('auteur.show', $auteur->slug) }}">{{ $auteur->nom_public }}</a></h2>
                                @if($auteur->titre_pro)<span class="muted small">{{ $auteur->titre_pro }}</span>@endif
                                <p>{{ Str::limit($auteur->bio, 220) }}</p>
                            </div>
                        </aside>
                    @endif

                    @unless($isPoll)
                        <div class="helpful js-helpful" data-url="{{ route('post.avis', $post->id) }}">
                            <p class="helpful__q" id="helpful-q">Cet article vous a-t-il aidé ?</p>
                            <div class="helpful__btns" role="group" aria-labelledby="helpful-q">
                                <button type="button" class="helpful__btn" data-utile="1" aria-pressed="{{ ($mon_avis ?? null) === true ? 'true' : 'false' }}"><i class="bi bi-hand-thumbs-up" aria-hidden="true"></i>Oui</button>
                                <button type="button" class="helpful__btn" data-utile="0" aria-pressed="{{ ($mon_avis ?? null) === false ? 'true' : 'false' }}"><i class="bi bi-hand-thumbs-down" aria-hidden="true"></i>Non</button>
                            </div>
                            <p class="helpful__thanks js-helpful-thanks" role="status" hidden>
                                <span class="js-helpful-yes">Merci ! Partagez-le à un proche qui pourrait en avoir besoin.</span>
                                <span class="js-helpful-no" hidden>Merci pour votre retour, il nous aide à améliorer nos articles.@if(config('sante.modules.questions')) Une question restée sans réponse ? <a href="{{ route('questions.index') }}#poser">Posez-la au médecin</a>.@endif</span>
                            </p>
                        </div>
                    @endunless

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
