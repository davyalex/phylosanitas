@extends('site.layout')
@section('title', Str::title($post->title ?? strip_tags($post->description ?? '')))
@section('description', Str::limit(strip_tags($post->description ?? ''), 160))
@section('image', asset($post->getFirstMediaUrl('image')))
@section('url', url()->full())

@section('content')

{{-- Barre de progression lecture --}}
<div id="reading-progress" class="reading-progress-bar"></div>

<article class="detail-section py-4 py-md-5">
    <div class="container-xl">
        <div class="row g-4 g-lg-5">

            {{-- Contenu principal --}}
            <div class="col-lg-8" data-aos="fade-up">

                {{-- Fil d'Ariane --}}
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb detail-breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('accueil') }}"><i class="bi bi-house-door me-1"></i>Accueil</a>
                        </li>
                        @if($post->category)
                            <li class="breadcrumb-item">
                                <a href="{{ route('post.list', ['category' => $post->category->slug]) }}">
                                    {{ Str::title($post->category->title) }}
                                </a>
                            </li>
                        @endif
                        <li class="breadcrumb-item active" aria-current="page">
                            {{ Str::limit($post->title ?? 'Article', 40) }}
                        </li>
                    </ol>
                </nav>

                {{-- Bloc article --}}
                <div class="detail-article">

                    {{-- Catégorie + Temps de lecture --}}
                    @php
                        $wordCount = str_word_count(strip_tags($post->description ?? ''));
                        $readMin   = max(1, round($wordCount / 200));
                        $catSlug   = strtolower($post->category->slug ?? '');
                        $catIcon   = match(true) {
                            str_contains($catSlug, 'actualite') => 'newspaper',
                            str_contains($catSlug, 'sondage')   => 'bar-chart-fill',
                            str_contains(strtolower($post->category->title ?? ''), 'sport') => 'heart-pulse-fill',
                            str_contains(strtolower($post->category->title ?? ''), 'obés')  => 'person-arms-up',
                            str_contains(strtolower($post->category->title ?? ''), 'scolaire') => 'mortarboard-fill',
                            default => 'file-medical-fill',
                        };
                    @endphp
                    <div class="detail-tags mb-3">
                        <a href="{{ route('post.list', ['category' => $post->category->slug ?? '']) }}"
                           class="detail-cat-badge">
                            <i class="bi bi-{{ $catIcon }} me-1"></i>
                            {{ Str::title($post->category->title ?? '') }}
                        </a>
                        <span class="detail-readtime">
                            <i class="bi bi-clock me-1"></i>{{ $readMin }} min de lecture
                        </span>
                    </div>

                    {{-- Titre --}}
                    <h1 class="detail-title">
                        {{ Str::title($post->title ?? strip_tags($post->description ?? '')) }}
                    </h1>

                    {{-- Meta --}}
                    <div class="detail-meta">
                        <span><i class="bi bi-calendar3"></i> {{ \Carbon\Carbon::parse($post->created_at)->format('d M Y') }}</span>
                        <span><i class="bi bi-eye-fill"></i> {{ number_format($post->views_count ?? 0) }} vue{{ ($post->views_count ?? 0) > 1 ? 's' : '' }}</span>
                        <span><i class="bi bi-chat-left-quote-fill"></i> {{ $post->commentaires_count }} commentaire{{ $post->commentaires_count > 1 ? 's' : '' }}</span>
                    </div>

                    {{-- Image principale --}}
                    @if($post->getFirstMediaUrl('image'))
                        <div class="detail-hero-img">
                            <img src="{{ asset($post->getFirstMediaUrl('image')) }}"
                                 alt="{{ $post->title }}"
                                 loading="eager"
                                 class="w-100">
                        </div>
                    @endif

                    {{-- Corps de l'article --}}
                    <div class="detail-body">
                        {!! $post->description !!}
                    </div>

                    {{-- Lien externe --}}
                    @if($post->lien)
                        <div class="detail-ext-link">
                            <i class="bi bi-link-45deg me-2"></i>
                            <a href="{{ $post->lien }}" target="_blank" rel="noopener noreferrer">
                                Consulter la source officielle
                                <i class="bi bi-box-arrow-up-right ms-1"></i>
                            </a>
                        </div>
                    @endif

                    {{-- Partage --}}
                    <div class="detail-share">
                        <span class="detail-share-label"><i class="bi bi-share-fill me-2"></i>Partager</span>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->full()) }}"
                           target="_blank" rel="noopener noreferrer" class="share-btn share-btn--fb"
                           aria-label="Partager sur Facebook">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->full()) }}&text={{ urlencode(Str::limit($post->title ?? '', 80)) }}"
                           target="_blank" rel="noopener noreferrer" class="share-btn share-btn--tw"
                           aria-label="Partager sur Twitter">
                            <i class="bi bi-twitter"></i>
                        </a>
                        <a href="https://wa.me/?text={{ urlencode(($post->title ?? '').' '.url()->full()) }}"
                           target="_blank" rel="noopener noreferrer" class="share-btn share-btn--wa"
                           aria-label="Partager sur WhatsApp">
                            <i class="bi bi-whatsapp"></i>
                        </a>
                        <button class="share-btn share-btn--copy"
                                onclick="navigator.clipboard.writeText('{{ url()->full() }}').then(()=>this.innerHTML='<i class=\'bi bi-check-lg\'></i>')"
                                aria-label="Copier le lien">
                            <i class="bi bi-link-45deg"></i>
                        </button>
                    </div>

                </div>{{-- /detail-article --}}

                {{-- Sondage --}}
                @if(strtolower($post->category->title ?? '') === 'sondage')
                    <div class="mt-5">
                        @if($sondage_total > 0)
                        <div class="detail-poll-results mb-4">
                            <h4 class="detail-poll-title">
                                <i class="bi bi-bar-chart-fill me-2"></i>Résultats du sondage
                                <span class="detail-poll-total">{{ number_format($sondage_total) }} participant{{ $sondage_total > 1 ? 's' : '' }}</span>
                            </h4>
                            @foreach($statistic_sondage as $key => $item)
                                @php
                                    $pct    = $sondage_total > 0 ? round(($item['choice'] * 100) / $sondage_total, 1) : 0;
                                    $colors = ['#0066CC','#00A86B','#17a2b8','#8E24AA'];
                                    $col    = $colors[$key % 4];
                                @endphp
                                <div class="poll-bar-item">
                                    <div class="poll-bar-label">
                                        <span>{{ ++$key }}. {{ $item['optionSondage']['title'] ?? '' }}</span>
                                        <span class="poll-bar-pct" style="color:{{ $col }}">{{ $pct }}%</span>
                                    </div>
                                    <div class="poll-bar-track">
                                        <div class="poll-bar-fill" style="width:{{ $pct }}%; background:{{ $col }};"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @endif

                        <div class="detail-poll-form">
                            <h4 class="detail-poll-title">
                                <i class="bi bi-hand-thumbs-up-fill me-2"></i>Participez au sondage
                            </h4>
                            <form action="{{ route('sondage.store') }}" method="post">
                                @csrf
                                <input type="hidden" name="post_id" value="{{ $post->id }}">
                                @foreach($post->optionSondages as $opt)
                                    <label class="poll-option" for="opt{{ $opt->id }}">
                                        <input class="poll-radio" type="radio"
                                               name="sondage_option"
                                               id="opt{{ $opt->id }}"
                                               value="{{ $opt->id }}" required>
                                        <span class="poll-option-label">{{ $opt->title }}</span>
                                    </label>
                                @endforeach
                                <button type="submit" class="btn btn-medical w-100 mt-3">
                                    <i class="bi bi-send-fill me-2"></i>Valider ma réponse
                                </button>
                            </form>
                        </div>
                    </div>
                @endif

                {{-- Commentaires --}}
                <div class="detail-comments mt-5">
                    <h3 class="detail-section-title">
                        <i class="bi bi-chat-left-quote-fill me-2"></i>
                        {{ $post->commentaires_count }}
                        Commentaire{{ $post->commentaires_count > 1 ? 's' : '' }}
                    </h3>

                    @forelse($post->commentaires as $item)
                        <div class="comment-item">
                            <div class="comment-avatar">
                                {{ strtoupper(substr($item->user_name ?? 'A', 0, 1)) }}
                            </div>
                            <div class="comment-content">
                                <div class="comment-header">
                                    <strong>{{ $item->user_name ?? 'Anonyme' }}</strong>
                                    <time class="comment-time">
                                        <i class="bi bi-clock me-1"></i>
                                        {{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}
                                    </time>
                                </div>
                                <p class="comment-text">{{ $item->message }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted fst-italic">Aucun commentaire pour le moment. Soyez le premier !</p>
                    @endforelse
                </div>

                {{-- Formulaire commentaire --}}
                <div class="detail-comment-form mt-4">
                    <h3 class="detail-section-title">
                        <i class="bi bi-pencil-square me-2"></i>Laisser un commentaire
                    </h3>
                    <form action="{{ route('post.comment') }}" method="POST">
                        @csrf
                        <input type="hidden" name="post_id" value="{{ $post->id }}">
                        @guest
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-person-fill me-1 text-medical-blue"></i>Votre nom
                                </label>
                                <input type="text" name="name" class="form-control"
                                       placeholder="Entrez votre nom" required>
                            </div>
                        @endguest
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-chat-left-text-fill me-1 text-medical-blue"></i>Votre message
                            </label>
                            <textarea name="message" class="form-control"
                                      placeholder="Partagez votre avis…" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-medical px-5">
                            <i class="bi bi-send-fill me-2"></i>Envoyer
                        </button>
                    </form>
                </div>

                {{-- Articles liés --}}
                @if(isset($post_last) && $post_last->count())
                <div class="detail-related mt-5">
                    <h3 class="detail-section-title">
                        <i class="bi bi-collection-fill me-2"></i>À lire aussi
                    </h3>
                    <div class="row g-3">
                        @foreach($post_last->take(3) as $rel)
                            <div class="col-12 col-sm-4">
                                <a href="{{ route('post.detail', ['slug' => $rel->slug]) }}"
                                   class="related-card text-decoration-none">
                                    <img src="{{ $rel->getFirstMediaUrl('image') ?: asset('assets_site/img/medc.jpg') }}"
                                         alt="{{ $rel->title }}" loading="lazy">
                                    <div class="related-card__body">
                                        <span class="related-card__cat">{{ $rel->category->title ?? '' }}</span>
                                        <p class="related-card__title">
                                            {{ Str::title(Str::limit($rel->title ?? '', 55)) }}
                                        </p>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

            </div>{{-- /col-lg-8 --}}

            {{-- Sidebar --}}
            <div class="col-lg-4">
                <div class="sidebar-wrapper sticky-sidebar">
                    @include('site.pages.sections.sidebar')
                </div>
            </div>

        </div>
    </div>
</article>

<script>
    // Barre de progression lecture
    document.addEventListener('scroll', function () {
        const el    = document.getElementById('reading-progress');
        const doc   = document.documentElement;
        const pct   = (doc.scrollTop / (doc.scrollHeight - doc.clientHeight)) * 100;
        if (el) el.style.width = Math.min(pct, 100) + '%';
    }, { passive: true });
</script>

@endsection
