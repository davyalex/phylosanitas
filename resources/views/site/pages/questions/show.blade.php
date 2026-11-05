@php
    $url   = route('questions.show', $question->slug);
    $plain = trim(preg_replace('/\s+/u', ' ', $question->reponse));
    $med   = $question->medecin && $question->medecin->profil_public ? $question->medecin : null;
@endphp

@extends('site.layout')
@section('title', $question->titre_affiche)
@section('description', Str::limit($plain, 155, '…'))
@section('canonical', $url)
@section('og_type', 'article')

@push('jsonld')
<script type="application/ld+json">
{!! json_encode([
    '@context'   => 'https://schema.org',
    '@type'      => 'QAPage',
    'mainEntity' => [
        '@type'       => 'Question',
        'name'        => $question->titre_affiche,
        'text'        => $question->question,
        'answerCount' => 1,
        'dateCreated' => $question->created_at->toIso8601String(),
        'author'      => ['@type' => 'Person', 'name' => 'Un lecteur'],
        'acceptedAnswer' => array_filter([
            '@type'       => 'Answer',
            'text'        => $plain,
            'dateCreated' => $question->repondu_at?->toIso8601String(),
            'url'         => $url,
            'upvoteCount' => 0,
            'author'      => $med
                ? ['@type' => 'Person', 'name' => $med->nom_public, 'url' => route('auteur.show', $med->slug)]
                : ['@type' => 'Organization', 'name' => config('app.name'), 'url' => url('/')],
        ]),
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@section('content')
<x-svc-hero icon="chat-heart" :titre="$question->titre_affiche" :parent="['Le médecin répond' => route('questions.index')]" courant="Question">
    <x-slot:extra>
        <div class="svc-hero__meta">
            @if($question->category)<span><i class="bi bi-tag" aria-hidden="true"></i>{{ $question->category->title }}</span>@endif
            <span><i class="bi bi-calendar3" aria-hidden="true"></i>Répondu le {{ $question->repondu_at?->translatedFormat('d F Y') }}</span>
            @if($question->vues > 10)<span><i class="bi bi-eye" aria-hidden="true"></i>{{ number_format($question->vues, 0, ',', ' ') }} lectures</span>@endif
        </div>
    </x-slot:extra>
</x-svc-hero>

<section class="svc-body">
    <div class="container-xl">
        <div class="layout layout--article">
            <div class="layout__main">
                <article class="qa-detail">
                    <div class="qa-detail__q">
                        <span class="qa-detail__label"><i class="bi bi-question-circle" aria-hidden="true"></i>La question</span>
                        <blockquote>{!! \App\Models\QuestionLecteur::enParagraphes($question->question) !!}</blockquote>
                        <p class="qa-detail__who">Un lecteur{{ $question->tranche_age ? ', ' . $question->tranche_age : '' }}</p>
                    </div>

                    <div class="qa-detail__a">
                        <span class="qa-detail__label"><i class="bi bi-chat-square-heart" aria-hidden="true"></i>La réponse</span>
                        @if($med)
                            <div class="byline">
                                <div class="byline__person">
                                    <span class="avatar">@if($med->photoUrl())<img src="{{ $med->photoUrl() }}" alt="" loading="lazy">@else{{ $med->initiales() }}@endif</span>
                                    <span><a href="{{ route('auteur.show', $med->slug) }}">{{ $med->nom_public }}</a>@if($med->titre_pro)<small>{{ $med->titre_pro }}</small>@endif</span>
                                </div>
                            </div>
                        @endif
                        <div class="prose">{!! \App\Models\QuestionLecteur::enParagraphes($question->reponse) !!}</div>
                    </div>

                    <aside class="notice" aria-label="Avertissement médical">
                        <i class="bi bi-info-circle" aria-hidden="true"></i>
                        <p>Cette réponse est générale et ne remplace pas une consultation : chaque situation est différente. En cas d'urgence, appelez le <a href="{{ $site['urgence_href'] }}"><strong>{{ $site['urgence'] }}</strong></a>.</p>
                    </aside>

                    <footer class="share">
                        <span class="share__label">Partager</span>
                        <a href="https://wa.me/?text={{ urlencode($question->titre_affiche . ' ' . $url) }}" target="_blank" rel="noopener noreferrer" class="share__btn" aria-label="Partager sur WhatsApp"><i class="bi bi-whatsapp"></i></a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($url) }}" target="_blank" rel="noopener noreferrer" class="share__btn" aria-label="Partager sur Facebook"><i class="bi bi-facebook"></i></a>
                        <button type="button" class="share__btn js-copy-link" data-url="{{ $url }}" aria-label="Copier le lien"><i class="bi bi-link-45deg" aria-hidden="true"></i></button>
                        <span class="share__feedback" role="status" aria-live="polite"></span>
                    </footer>
                </article>

                <div class="notify-card">
                    <span class="notify-card__icon" aria-hidden="true"><i class="bi bi-pencil-square"></i></span>
                    <div class="notify-card__body">
                        <strong>Vous avez une autre question ?</strong>
                        <span>Gratuit et anonyme : un professionnel de santé vous répond.</span>
                    </div>
                    <a href="{{ route('questions.index') }}#poser" class="btn btn-medical btn-sm">Poser ma question</a>
                </div>

                @if($autres->count())
                    <div class="mini-head"><h2>Autres questions</h2><a href="{{ route('questions.index') }}" class="sec-head__link">Toutes les réponses <i class="bi bi-arrow-right" aria-hidden="true"></i></a></div>
                    <div class="qa-list">
                        @foreach($autres as $a)
                            <article class="qa-item">
                                <div class="qa-item__q"><h3><a href="{{ route('questions.show', $a->slug) }}">{{ $a->titre_affiche }}</a></h3></div>
                            </article>
                        @endforeach
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
