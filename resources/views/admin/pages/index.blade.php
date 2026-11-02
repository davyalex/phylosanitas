@extends('admin.layout')
@section('title', 'Tableau de bord')

@section('page_header')
@php
    $h = now()->hour;
    $hello = $h < 18 ? 'Bonjour' : 'Bonsoir';
@endphp
<div class="page-bar">
    <div>
        <h1 class="page-bar__title">{{ $hello }}, {{ Str::before(Auth::user()->name, ' ') }}</h1>
        <p class="page-bar__sub">{{ ucfirst(now()->translatedFormat('l d F Y')) }} · voici l'activité de {{ config('app.name') }}.</p>
    </div>
</div>
<div class="quick">
    <a href="{{ route('post.create') }}" class="quick__primary"><i class="bi bi-plus-lg"></i>Nouvel article</a>
    <a href="{{ route('post.create', ['type' => 'sondage']) }}"><i class="bi bi-bar-chart-line"></i>Nouveau sondage</a>
    <a href="{{ route('flash.index') }}"><i class="bi bi-lightning-charge"></i>Info flash</a>
    @role('administrateur')
        <a href="{{ route('publicite.create') }}"><i class="bi bi-badge-ad"></i>Publicité</a>
        <a href="{{ route('actualite.index') }}"><i class="bi bi-images"></i>Carrousel</a>
    @endrole
    <a href="{{ route('accueil') }}" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right"></i>Voir le site</a>
</div>
@endsection

@section('content')
@php
    $fmt = fn($n) => number_format((int) $n, 0, ',', ' ');
    $ctr = $ads['impressions'] > 0 ? round($ads['clics'] / $ads['impressions'] * 100, 2) : 0;
@endphp

{{-- ① Indicateurs --}}
<div class="kpis">
    <div class="kpi">
        <div class="kpi__top"><span class="kpi__label">Visites · 30 jours</span><span class="kpi__icon"><i class="bi bi-people"></i></span></div>
        <div class="kpi__value">{{ $fmt($audience['visits_30']) }}</div>
        <div class="kpi__meta">
            @if(!is_null($audience['trend']))
                <span class="{{ $audience['trend'] >= 0 ? 'kpi__trend--up' : 'kpi__trend--down' }}">{{ $audience['trend'] >= 0 ? '+' : '' }}{{ $audience['trend'] }}&nbsp;%</span> vs 30 j précédents ·
            @endif
            <strong>{{ $fmt($audience['today']) }}</strong> aujourd'hui
        </div>
    </div>
    <a href="{{ route('post') }}" class="kpi">
        <div class="kpi__top"><span class="kpi__label">Articles en ligne</span><span class="kpi__icon kpi__icon--green"><i class="bi bi-file-earmark-medical"></i></span></div>
        <div class="kpi__value">{{ $fmt($content['publies']) }}</div>
        <div class="kpi__meta"><strong>{{ $content['brouillons'] }}</strong> brouillon{{ $content['brouillons'] > 1 ? 's' : '' }} · {{ $content['publies_30j'] }} publié{{ $content['publies_30j'] > 1 ? 's' : '' }} ce mois</div>
    </a>
    @role('administrateur')
    <a href="{{ route('messages.index', ['status' => 'new']) }}" class="kpi">
        <div class="kpi__top"><span class="kpi__label">Messages non lus</span><span class="kpi__icon {{ $inbox['new'] ? 'kpi__icon--red' : '' }}"><i class="bi bi-envelope"></i></span></div>
        <div class="kpi__value">{{ $fmt($inbox['new']) }}</div>
        <div class="kpi__meta"><strong>{{ $fmt($inbox['abonnes']) }}</strong> abonnés newsletter</div>
    </a>
    <a href="{{ route('publicite.index') }}" class="kpi">
        <div class="kpi__top"><span class="kpi__label">Publicités en diffusion</span><span class="kpi__icon kpi__icon--amber"><i class="bi bi-badge-ad"></i></span></div>
        <div class="kpi__value">{{ $ads['live'] }}</div>
        <div class="kpi__meta"><strong>{{ $fmt($ads['clics']) }}</strong> clics · {{ number_format($ctr, 2, ',', ' ') }}&nbsp;% de taux de clic</div>
    </a>
    @else
    <div class="kpi">
        <div class="kpi__top"><span class="kpi__label">Commentaires</span><span class="kpi__icon"><i class="bi bi-chat-left-text"></i></span></div>
        <div class="kpi__value">{{ $fmt($content['commentaires']) }}</div>
    </div>
    <div class="kpi">
        <div class="kpi__top"><span class="kpi__label">Vues d'articles</span><span class="kpi__icon"><i class="bi bi-eye"></i></span></div>
        <div class="kpi__value">{{ $fmt($audience['article_views']) }}</div>
    </div>
    @endrole
</div>

{{-- Alertes utiles --}}
@if($ads['expiring']->count() || $flash_live === 0)
    <div class="d-flex flex-column gap-2 mb-4">
        @foreach($ads['expiring'] as $exp)
            <div class="alert alert-warning d-flex align-items-center gap-2 mb-0 py-2">
                <i class="bi bi-hourglass-split"></i>
                <span>La publicité <strong>{{ $exp->titre }}</strong> se termine {{ $exp->fin_at->diffForHumans() }}.</span>
                @role('administrateur')<a href="{{ route('publicite.edit', $exp->id) }}" class="ms-auto small fw-semibold">Prolonger</a>@endrole
            </div>
        @endforeach
        @if($flash_live === 0)
            <div class="alert alert-light border d-flex align-items-center gap-2 mb-0 py-2">
                <i class="bi bi-lightning-charge text-primary"></i>
                <span>Aucune info flash en ligne : le bandeau du site est masqué.</span>
                <a href="{{ route('flash.index') }}" class="ms-auto small fw-semibold">Publier une info</a>
            </div>
        @endif
    </div>
@endif

<div class="dash-grid">
    {{-- Colonne principale --}}
    <div class="d-flex flex-column gap-3" style="min-width:0">

        <section class="panel-a">
            <div class="panel-a__head">
                <h2 class="panel-a__title"><i class="bi bi-graph-up"></i>Visites par jour · 30 derniers jours</h2>
                <span class="small text-muted">{{ $fmt($audience['pages_30']) }} pages vues · {{ $fmt($audience['total']) }} visites au total</span>
            </div>
            <div class="panel-a__body">
                @if($audience['visits_30'] === 0)
                    <div class="empty-admin py-4">
                        <i class="bi bi-bar-chart"></i>
                        <h3>Les statistiques démarrent</h3>
                        <p>Le compteur de visites vient d'être activé : les données apparaîtront dès les premières visites.</p>
                    </div>
                @else
                    <div class="chart-box">
                        <canvas id="visitsChart" role="img" aria-label="Visites par jour sur 30 jours : {{ $fmt($audience['visits_30']) }} visites au total, {{ $fmt($audience['today']) }} aujourd'hui."></canvas>
                    </div>
                    <details class="mt-2 small">
                        <summary class="text-muted">Voir les données en tableau</summary>
                        <div class="table-responsive mt-2" style="max-height:240px">
                            <table class="table table-sm mb-0">
                                <thead><tr><th>Jour</th><th class="text-end">Visites</th><th class="text-end">Pages vues</th></tr></thead>
                                <tbody>
                                    @foreach(array_reverse(array_keys($audience['labels'])) as $i)
                                        <tr><td>{{ $audience['labels'][$i] }}</td><td class="text-end">{{ $audience['visits'][$i] }}</td><td class="text-end">{{ $audience['pages'][$i] }}</td></tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </details>
                @endif
            </div>
        </section>

        <section class="panel-a">
            <div class="panel-a__head">
                <h2 class="panel-a__title"><i class="bi bi-trophy"></i>Articles les plus lus</h2>
                <a href="{{ route('post') }}" class="panel-a__link">Tous les articles</a>
            </div>
            @if($most_viewed_posts->isEmpty())
                <p class="text-muted text-center py-4 mb-0">Pas encore de lectures enregistrées.</p>
            @else
                <ul class="mini-list">
                    @foreach($most_viewed_posts as $i => $p)
                        <li>
                            <span class="rank">{{ $i + 1 }}</span>
                            <img src="{{ $p->getFirstMediaUrl('image') ?: asset('assets_site/img/medc.jpg') }}" alt="" loading="lazy">
                            <div class="mini-list__main">
                                <a href="{{ route('post.detail', ['slug' => $p->slug]) }}" target="_blank" rel="noopener" class="mini-list__title">{{ $p->title ?: Str::limit(strip_tags($p->description), 70) }}</a>
                                <span class="mini-list__sub">{{ $p->category->title ?? '—' }}</span>
                            </div>
                            <span class="mini-list__num">{{ $fmt($p->views_total) }} <span class="text-muted fw-normal small">vue{{ $p->views_total > 1 ? 's' : '' }}</span></span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        <section class="panel-a">
            <div class="panel-a__head">
                <h2 class="panel-a__title"><i class="bi bi-clock-history"></i>Derniers articles</h2>
                <a href="{{ route('post.create') }}" class="panel-a__link"><i class="bi bi-plus-lg"></i> Nouvel article</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th class="ps-3">Article</th><th class="table-admin-hide">Auteur</th><th>Statut</th><th class="text-end pe-3"><span class="visually-hidden">Actions</span></th></tr></thead>
                    <tbody>
                        @forelse($post_recent as $item)
                            <tr>
                                <td class="ps-3">
                                    <span class="fw-semibold d-block">{{ Str::limit($item->title, 60) }}</span>
                                    <span class="small text-muted">{{ $item->category->title ?? '—' }} · {{ $item->created_at->diffForHumans() }} · {{ $item->commentaires_count }} comm.</span>
                                </td>
                                <td class="small text-muted table-admin-hide">{{ $item->user->name ?? '—' }}</td>
                                <td><span class="status {{ $item->published === 'public' ? 'status--live' : 'status--off' }}">{{ $item->published === 'public' ? 'En ligne' : 'Brouillon' }}</span></td>
                                <td class="text-end pe-3"><a href="{{ route('post.edit', $item->slug) }}" class="btn btn-sm btn-outline-primary icon-action" aria-label="Modifier"><i class="bi bi-pencil"></i></a></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">Aucun article.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    {{-- Colonne latérale --}}
    <div class="d-flex flex-column gap-3" style="min-width:0">

        @role('administrateur')
        <section class="panel-a">
            <div class="panel-a__head">
                <h2 class="panel-a__title"><i class="bi bi-envelope"></i>Messages</h2>
                <a href="{{ route('messages.index') }}" class="panel-a__link">Boîte de réception</a>
            </div>
            @if($inbox['latest']->isEmpty())
                <p class="text-muted text-center py-4 mb-0 small">Aucun message reçu.</p>
            @else
                <ul class="mini-list">
                    @foreach($inbox['latest'] as $m)
                        <li>
                            <div class="mini-list__main">
                                <a href="{{ route('messages.show', $m) }}" class="mini-list__title">{{ $m->subject }}</a>
                                <span class="mini-list__sub">{{ $m->name }} · {{ $m->created_at->diffForHumans() }}</span>
                            </div>
                            @if($m->status === 'new')<span class="status status--new">Non lu</span>@endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
        @endrole

        <section class="panel-a">
            <div class="panel-a__head">
                <h2 class="panel-a__title"><i class="bi bi-chat-left-text"></i>Commentaires récents</h2>
                <span class="small text-muted">{{ $fmt($content['commentaires']) }} au total</span>
            </div>
            @if($recent_comments->isEmpty())
                <p class="text-muted text-center py-4 mb-0 small">Aucun commentaire.</p>
            @else
                <ul class="mini-list">
                    @foreach($recent_comments as $c)
                        <li>
                            <div class="mini-list__main">
                                <span class="mini-list__title">{{ $c->user_name ?: 'Anonyme' }} <span class="fw-normal text-muted">· {{ \Carbon\Carbon::parse($c->created_at)->diffForHumans() }}</span></span>
                                <span class="mini-list__sub d-block text-truncate">« {{ Str::limit($c->message, 80) }} »</span>
                                <a href="{{ route('post.detail', ['slug' => $c->post_slug]) }}#comments" target="_blank" rel="noopener" class="small text-truncate d-block">{{ Str::limit($c->post_title, 50) }}</a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        <section class="panel-a">
            <div class="panel-a__head">
                <h2 class="panel-a__title"><i class="bi bi-folder2"></i>Articles par catégorie</h2>
            </div>
            <div class="panel-a__body">
                @php $maxCat = max(1, $posts_by_category->max('posts_count')); @endphp
                @foreach($posts_by_category as $cat)
                    <div class="bar-row">
                        <div class="bar-row__label"><span>{{ $cat->title }}</span><strong>{{ $cat->posts_count }}</strong></div>
                        <div class="bar-row__track"><div class="bar-row__fill" style="width: {{ round($cat->posts_count / $maxCat * 100) }}%"></div></div>
                    </div>
                @endforeach
            </div>
        </section>

        @if($views_by_country->isNotEmpty())
            <section class="panel-a">
                <div class="panel-a__head"><h2 class="panel-a__title"><i class="bi bi-globe2"></i>Lectures par pays</h2></div>
                <div class="panel-a__body">
                    @php $maxC = max(1, $views_by_country->max('count')); @endphp
                    @foreach($views_by_country as $country)
                        <div class="bar-row">
                            <div class="bar-row__label"><span>{{ $country->country }}</span><strong>{{ $fmt($country->count) }}</strong></div>
                            <div class="bar-row__track"><div class="bar-row__fill" style="width: {{ round($country->count / $maxC * 100) }}%"></div></div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($audience['visits_30'] > 0)
<script>
document.addEventListener('DOMContentLoaded', function () {
    var el = document.getElementById('visitsChart');
    if (!el || typeof Chart === 'undefined') return;
    var pages = @json($audience['pages']);
    new Chart(el, {
        type: 'bar',
        data: {
            labels: @json($audience['labels']),
            datasets: [{
                label: 'Visites',
                data: @json($audience['visits']),
                backgroundColor: '#0066CC',
                hoverBackgroundColor: '#004A99',
                borderRadius: { topLeft: 4, topRight: 4 },
                borderSkipped: 'bottom',
                maxBarThickness: 18,
                categoryPercentage: .8,
                barPercentage: .9
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#12233a', padding: 10, displayColors: false,
                    titleFont: { weight: '600' },
                    callbacks: {
                        label: function (ctx) { return ctx.parsed.y + ' visite' + (ctx.parsed.y > 1 ? 's' : ''); },
                        afterLabel: function (ctx) { var p = pages[ctx.dataIndex]; return p + ' page' + (p > 1 ? 's' : '') + ' vue' + (p > 1 ? 's' : ''); }
                    }
                }
            },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#5f6f84', maxRotation: 0, autoSkip: true, maxTicksLimit: 8 } },
                y: { beginAtZero: true, border: { display: false }, grid: { color: '#eef1f5' }, ticks: { color: '#5f6f84', precision: 0, maxTicksLimit: 5 } }
            }
        }
    });
});
</script>
@endif
@endpush
