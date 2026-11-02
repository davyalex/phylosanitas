@extends('admin.layout')
@php
    $title    = $post->title ?: Str::limit(strip_tags($post->description ?? ''), 90);
    $online   = $post->published === 'public';
    $plain    = trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($post->description ?? ''), ENT_QUOTES, 'UTF-8')));
    $words    = str_word_count($plain);
    $editUrl  = $isPoll ? route('post.edit-sondage', $post->id) : route('post.edit', $post->slug);
    $listUrl  = $isPoll ? route('post', ['type' => 'sondage']) : route('post');
    $fmt      = fn($n) => number_format((int) $n, 0, ',', ' ');
    $imgMedia = $post->getFirstMedia('image');
    $img      = $imgMedia ? ($imgMedia->hasGeneratedConversion('thumb') ? $imgMedia->getUrl('thumb') : $imgMedia->getUrl()) : null;
@endphp
@section('title', $isPoll ? 'Détail du sondage' : 'Détail de l\'article')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ $listUrl }}">{{ $isPoll ? 'Sondages' : 'Articles' }}</a></li>@endsection

@section('page_header')
<div class="page-bar">
    <div style="min-width:0; flex:1">
        <h1 class="page-bar__title">{{ $title }}</h1>
        <p class="page-bar__sub d-flex flex-wrap align-items-center gap-2">
            <span class="status {{ $online ? 'status--live' : 'status--off' }}">{{ $online ? 'En ligne' : 'Brouillon' }}</span>
            @if($post->actualite_une)<span class="status status--scheduled">À la une</span>@endif
            <span>{{ $post->category->title ?? '—' }}</span>
            <span>· {{ $post->user->name ?? 'Auteur inconnu' }}</span>
            <span>· publié {{ $post->created_at->translatedFormat('d M Y') }}</span>
            @if($post->updated_at && $post->updated_at->gt($post->created_at->copy()->addMinute()))<span>· modifié {{ $post->updated_at->diffForHumans() }}</span>@endif
        </p>
    </div>
    <div class="page-bar__actions">
        <a href="{{ route('post.detail', ['slug' => $post->slug]) }}" target="_blank" rel="noopener" class="btn btn-outline-secondary"><i class="bi bi-box-arrow-up-right me-1"></i>Voir sur le site</a>
        <a href="{{ $editUrl }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Modifier</a>
        <div class="dropdown">
            <button class="btn btn-light border" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Plus d'actions"><i class="bi bi-three-dots"></i></button>
            <ul class="dropdown-menu dropdown-menu-end action-menu">
                <li><a class="dropdown-item" href="{{ route('post.published', $post->id) }}">@if($online)<i class="bi bi-eye-slash"></i>Mettre en brouillon @else<i class="bi bi-send"></i>Publier @endif</a></li>
                @unless($isPoll)
                    <li><a class="dropdown-item" href="{{ url('/admin/post/actualite') }}?actualite_une={{ $post->actualite_une ? 0 : 1 }}&actualite={{ $post->id }}"><i class="bi bi-star{{ $post->actualite_une ? '-fill' : '' }}"></i>{{ $post->actualite_une ? 'Retirer de la une' : 'Mettre à la une' }}</a></li>
                @endunless
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('post.delete', $post->id) }}" method="POST" data-confirm="Supprimer définitivement {{ $isPoll ? 'ce sondage' : 'cet article' }} ?">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-trash"></i>Supprimer</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection

@section('content')

{{-- Indicateurs --}}
<div class="kpis">
    <div class="kpi">
        <div class="kpi__top"><span class="kpi__label">Vues totales</span><span class="kpi__icon"><i class="bi bi-eye"></i></span></div>
        <div class="kpi__value">{{ $fmt($stats['total']) }}</div>
        <div class="kpi__meta"><strong>{{ $fmt($stats['uniques']) }}</strong> lecteur{{ $stats['uniques'] > 1 ? 's' : '' }} unique{{ $stats['uniques'] > 1 ? 's' : '' }}</div>
    </div>
    <div class="kpi">
        <div class="kpi__top"><span class="kpi__label">7 derniers jours</span><span class="kpi__icon kpi__icon--green"><i class="bi bi-graph-up"></i></span></div>
        <div class="kpi__value">{{ $fmt($stats['last7']) }}</div>
        <div class="kpi__meta"><strong>{{ $fmt($stats['last30']) }}</strong> sur 30 jours</div>
    </div>
    @if($isPoll)
        <div class="kpi">
            <div class="kpi__top"><span class="kpi__label">Participants au sondage</span><span class="kpi__icon kpi__icon--amber"><i class="bi bi-people"></i></span></div>
            <div class="kpi__value">{{ $fmt($poll['total']) }}</div>
            <div class="kpi__meta">
                @if($stats['total'] > 0)<strong>{{ round($poll['total'] / $stats['total'] * 100) }}&nbsp;%</strong> des visiteurs ont voté @else Pas encore de vues @endif
            </div>
        </div>
    @else
        <div class="kpi">
            <div class="kpi__top"><span class="kpi__label">Longueur</span><span class="kpi__icon kpi__icon--amber"><i class="bi bi-text-paragraph"></i></span></div>
            <div class="kpi__value">{{ $fmt($words) }} <span class="fs-6 fw-semibold text-muted">mots</span></div>
            <div class="kpi__meta"><strong>{{ max(1, (int) round($words / 200)) }} min</strong> de lecture</div>
        </div>
    @endif
    <div class="kpi">
        <div class="kpi__top"><span class="kpi__label">Commentaires</span><span class="kpi__icon"><i class="bi bi-chat-left-text"></i></span></div>
        <div class="kpi__value">{{ $fmt($post->commentaires_count) }}</div>
        <div class="kpi__meta">@if($stats['last'])Dernière lecture {{ \Carbon\Carbon::parse($stats['last'])->diffForHumans() }}@else Aucune lecture @endif</div>
    </div>
</div>

<div class="dash-grid">
    <div class="d-flex flex-column gap-3" style="min-width:0">

        {{-- Vues par jour --}}
        <section class="panel-a">
            <div class="panel-a__head">
                <h2 class="panel-a__title"><i class="bi bi-bar-chart"></i>Vues par jour · 30 derniers jours</h2>
                <span class="small text-muted">{{ $fmt($stats['last30']) }} vues</span>
            </div>
            <div class="panel-a__body">
                @if($stats['last30'] === 0)
                    <p class="text-muted text-center py-4 mb-0">Aucune vue sur les 30 derniers jours.</p>
                @else
                    <div class="chart-box" style="height:220px">
                        <canvas id="postViewsChart" role="img" aria-label="Vues par jour sur 30 jours : {{ $stats['last30'] }} vues au total."></canvas>
                    </div>
                @endif
            </div>
        </section>

        {{-- Résultats du sondage --}}
        @if($isPoll)
            <section class="panel-a">
                <div class="panel-a__head">
                    <h2 class="panel-a__title"><i class="bi bi-ui-radios"></i>Résultats</h2>
                    <span class="small text-muted">{{ $fmt($poll['total']) }} vote{{ $poll['total'] > 1 ? 's' : '' }}</span>
                </div>
                <div class="panel-a__body">
                    @forelse($poll['options'] as $opt)
                        <div class="bar-row">
                            <div class="bar-row__label"><span>{{ $opt['title'] }}</span><strong>{{ $opt['pct'] }}&nbsp;% <span class="text-muted fw-normal">({{ $opt['votes'] }})</span></strong></div>
                            <div class="bar-row__track"><div class="bar-row__fill" style="width: {{ $opt['pct'] }}%"></div></div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Aucune option de réponse.</p>
                    @endforelse
                </div>
            </section>
        @endif

        {{-- Commentaires --}}
        <section class="panel-a">
            <div class="panel-a__head">
                <h2 class="panel-a__title"><i class="bi bi-chat-left-text"></i>Commentaires</h2>
                <span class="small text-muted">{{ $comments->count() }}</span>
            </div>
            @if($comments->isEmpty())
                <p class="text-muted text-center py-4 mb-0">Aucun commentaire pour le moment.</p>
            @else
                <ul class="mini-list">
                    @foreach($comments as $c)
                        <li class="align-items-start">
                            <span class="inbox-row__avatar flex-shrink-0" aria-hidden="true">{{ strtoupper(mb_substr($c->user_name ?: 'A', 0, 1)) }}</span>
                            <div class="mini-list__main">
                                <span class="mini-list__title">{{ $c->user_name ?: 'Anonyme' }} <span class="fw-normal text-muted">· {{ $c->created_at->diffForHumans() }}</span></span>
                                @if($c->user_email)<span class="mini-list__sub d-block">{{ $c->user_email }}</span>@endif
                                <p class="mb-0 mt-1 small" style="white-space: pre-line">{{ $c->message }}</p>
                            </div>
                            <form action="{{ route('post.comment.delete', $c->id) }}" method="POST" data-confirm="Supprimer ce commentaire ?">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger icon-action" title="Supprimer le commentaire" aria-label="Supprimer le commentaire"><i class="bi bi-trash"></i></button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>

    {{-- Colonne latérale --}}
    <div class="d-flex flex-column gap-3" style="min-width:0">
        <section class="panel-a">
            @if($img)
                <img src="{{ $img }}" alt="" style="width:100%; aspect-ratio:16/9; object-fit:cover; border-radius: 12px 12px 0 0; display:block;">
            @endif
            <div class="panel-a__body">
                <h2 class="panel-a__title mb-2"><i class="bi bi-google"></i>Aperçu Google</h2>
                <div class="serp">
                    <span class="serp__url">{{ parse_url(config('app.url'), PHP_URL_HOST) }} › article › {{ $post->slug }}</span>
                    <span class="serp__title">{{ Str::limit($title, 65) }} — {{ config('app.name') }}</span>
                    <span class="serp__desc">{{ $post->meta_description ?: Str::limit($plain, 158) }}</span>
                </div>
                @if(!$isPoll && !$post->meta_description)
                    <p class="small text-muted mt-2 mb-0"><i class="bi bi-lightbulb me-1"></i>Ajoutez un « résumé pour Google » dans <a href="{{ $editUrl }}">Modifier</a> pour un meilleur référencement.</p>
                @endif
            </div>
        </section>

        <section class="panel-a">
            <div class="panel-a__head"><h2 class="panel-a__title"><i class="bi bi-info-circle"></i>Informations</h2></div>
            <ul class="mini-list">
                <li><div class="mini-list__main"><span class="mini-list__sub">Adresse</span><a href="{{ route('post.detail', ['slug' => $post->slug]) }}" target="_blank" rel="noopener" class="mini-list__title">/article/{{ $post->slug }}</a></div></li>
                <li><div class="mini-list__main"><span class="mini-list__sub">Première lecture</span><span class="mini-list__title">{{ $stats['first'] ? \Carbon\Carbon::parse($stats['first'])->translatedFormat('d M Y à H:i') : '—' }}</span></div></li>
                <li><div class="mini-list__main"><span class="mini-list__sub">Dernière lecture</span><span class="mini-list__title">{{ $stats['last'] ? \Carbon\Carbon::parse($stats['last'])->translatedFormat('d M Y à H:i') : '—' }}</span></div></li>
                @if($post->lien)
                    <li><div class="mini-list__main"><span class="mini-list__sub">Source externe</span><a href="{{ $post->lien }}" target="_blank" rel="noopener" class="mini-list__title">{{ $post->lien }}</a></div></li>
                @endif
            </ul>
        </section>

        <section class="panel-a">
            <div class="panel-a__head"><h2 class="panel-a__title"><i class="bi bi-globe2"></i>Lectures par pays</h2></div>
            <div class="panel-a__body">
                @php $maxC = max(1, $countries->max('n')); @endphp
                @forelse($countries as $c)
                    <div class="bar-row">
                        <div class="bar-row__label"><span>{{ $c->pays }}</span><strong>{{ $fmt($c->n) }}</strong></div>
                        <div class="bar-row__track"><div class="bar-row__fill" style="width: {{ round($c->n / $maxC * 100) }}%"></div></div>
                    </div>
                @empty
                    <p class="text-muted small mb-0">Pas encore de données.</p>
                @endforelse
            </div>
        </section>
    </div>
</div>

<a href="{{ $listUrl }}" class="btn btn-link px-0 mt-3"><i class="bi bi-arrow-left me-1"></i>Retour à la liste</a>
@endsection

@push('scripts')
@if($stats['last30'] > 0)
<script>
document.addEventListener('DOMContentLoaded', function () {
    var el = document.getElementById('postViewsChart');
    if (!el || typeof Chart === 'undefined') return;
    new Chart(el, {
        type: 'bar',
        data: {
            labels: @json($chart['labels']),
            datasets: [{ label: 'Vues', data: @json($chart['values']), backgroundColor: '#0066CC', hoverBackgroundColor: '#004A99', borderRadius: { topLeft: 4, topRight: 4 }, borderSkipped: 'bottom', maxBarThickness: 18 }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: { legend: { display: false }, tooltip: { backgroundColor: '#12233a', displayColors: false, callbacks: { label: function (c) { return c.parsed.y + ' vue' + (c.parsed.y > 1 ? 's' : ''); } } } },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#5f6f84', maxRotation: 0, autoSkip: true, maxTicksLimit: 8 } },
                y: { beginAtZero: true, grid: { color: '#eef1f5' }, ticks: { color: '#5f6f84', precision: 0, maxTicksLimit: 5 } }
            }
        }
    });
});
</script>
@endif
@endpush
