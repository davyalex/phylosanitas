@extends('admin.layout')
@section('title', 'Tableau de bord')

@section('content')
<section class="section dashboard">

    {{-- ① KPI Cards --}}
    <div class="row g-3 mb-4">

        <div class="col-6 col-xl-3">
            <div class="kpi-card kpi-blue">
                <div class="kpi-icon"><i class="bi bi-card-text"></i></div>
                <div class="kpi-body">
                    <p class="kpi-label">Articles publiés</p>
                    <h3 class="kpi-value">{{ number_format($post_count) }}</h3>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="kpi-card kpi-green">
                <div class="kpi-icon"><i class="bi bi-collection-fill"></i></div>
                <div class="kpi-body">
                    <p class="kpi-label">Catégories</p>
                    <h3 class="kpi-value">{{ number_format($category_count) }}</h3>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="kpi-card kpi-teal">
                <div class="kpi-icon"><i class="bi bi-people-fill"></i></div>
                <div class="kpi-body">
                    <p class="kpi-label">Visiteurs uniques</p>
                    <h3 class="kpi-value">{{ number_format($countVisitor) }}</h3>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="kpi-card kpi-purple">
                <div class="kpi-icon"><i class="bi bi-eye-fill"></i></div>
                <div class="kpi-body">
                    <p class="kpi-label">Total vues</p>
                    <h3 class="kpi-value">{{ number_format($total_views) }}</h3>
                </div>
            </div>
        </div>

    </div>

    <div class="row g-3">

        {{-- ② Top articles + Graphique publications --}}
        <div class="col-xl-8">

            {{-- Top 10 articles --}}
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-trophy-fill text-warning me-2"></i>Articles les plus visités</h6>
                    <a href="{{ route('post') }}" class="btn btn-sm btn-outline-primary">Voir tous</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">#</th>
                                <th>Article</th>
                                <th>Catégorie</th>
                                <th class="text-center">Vues</th>
                                <th class="text-center">Commentaires</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($most_viewed_posts as $i => $post)
                                <tr>
                                    <td class="ps-3 fw-bold text-muted">{{ $i + 1 }}</td>
                                    <td>
                                        <a href="{{ route('post.detail', ['slug' => $post['slug']]) }}"
                                           target="_blank"
                                           class="text-decoration-none text-dark fw-semibold">
                                            {{ Str::limit($post['title'], 48) }}
                                        </a>
                                    </td>
                                    <td><span class="badge bg-primary bg-opacity-10 text-primary">{{ $post['category'] }}</span></td>
                                    <td class="text-center">
                                        <span class="badge bg-info text-white">
                                            <i class="bi bi-eye me-1"></i>{{ number_format($post['views']) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success text-white">
                                            <i class="bi bi-chat-left-quote me-1"></i>{{ $post['comments'] }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Aucun article publié.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Graphique publications 30 jours --}}
            <div class="card">
                <div class="card-header py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-bar-chart-fill text-primary me-2"></i>Publications — 30 derniers jours</h6>
                </div>
                <div class="card-body">
                    <canvas id="publicationsChart" style="max-height:240px;"></canvas>
                </div>
            </div>

        </div>

        {{-- ③ Sidebar widgets --}}
        <div class="col-xl-4">

            {{-- Répartition catégories --}}
            <div class="card mb-3">
                <div class="card-header py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-pie-chart-fill text-success me-2"></i>Répartition par catégorie</h6>
                </div>
                <div class="card-body pb-2">
                    <canvas id="categoryChart" style="max-height:200px;" class="mb-3"></canvas>
                    @foreach($posts_by_category as $cat)
                        <div class="d-flex justify-content-between align-items-center mb-2 small">
                            <span class="text-truncate me-2">{{ $cat->title }}</span>
                            <span class="badge bg-secondary rounded-pill flex-shrink-0">{{ $cat->posts_count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Vues par pays --}}
            <div class="card mb-3">
                <div class="card-header py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-globe2 text-info me-2"></i>Vues par pays</h6>
                </div>
                <div class="card-body p-0">
                    @if($views_by_country->isEmpty())
                        <p class="text-muted text-center py-3 small">Aucune donnée géographique disponible.</p>
                    @else
                        @php $totalPays = $views_by_country->sum('count'); @endphp
                        <ul class="list-group list-group-flush">
                            @foreach($views_by_country as $country)
                                @php $pct = $totalPays > 0 ? round($country->count / $totalPays * 100) : 0; @endphp
                                <li class="list-group-item px-3 py-2">
                                    <div class="d-flex justify-content-between small mb-1">
                                        <span><i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $country->country ?: 'Inconnu' }}</span>
                                        <span class="fw-semibold">{{ number_format($country->count) }} <span class="text-muted">({{ $pct }}%)</span></span>
                                    </div>
                                    <div class="progress" style="height:4px;">
                                        <div class="progress-bar bg-primary" style="width:{{ $pct }}%;"></div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            {{-- Commentaires récents --}}
            <div class="card">
                <div class="card-header py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-chat-left-quote-fill text-warning me-2"></i>Commentaires récents</h6>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($recent_comments as $comment)
                            <li class="list-group-item px-3 py-2">
                                <p class="mb-1 small fw-semibold text-truncate">{{ $comment->user_name }}</p>
                                <p class="mb-1 small text-muted text-truncate">{{ Str::limit($comment->message, 60) }}</p>
                                <a href="{{ route('post.detail', ['slug' => $comment->post_slug]) }}"
                                   target="_blank"
                                   class="small text-primary text-decoration-none text-truncate d-block">
                                    <i class="bi bi-link-45deg"></i> {{ Str::limit($comment->post_title, 40) }}
                                </a>
                            </li>
                        @empty
                            <li class="list-group-item text-center text-muted py-3 small">Aucun commentaire.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

        </div>

    </div>

    {{-- ④ Derniers posts publiés --}}
    <div class="card mt-3">
        <div class="card-header d-flex justify-content-between align-items-center py-3">
            <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history text-primary me-2"></i>Derniers articles publiés</h6>
            <a href="{{ route('post') }}" class="btn btn-sm btn-outline-primary">Gérer les articles</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width:55px;"></th>
                        <th>Titre</th>
                        <th>Catégorie</th>
                        <th>Auteur</th>
                        <th>Date</th>
                        <th class="text-center">Commentaires</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($post_recent as $item)
                        <tr>
                            <td class="ps-3">
                                <img src="{{ $item->getFirstMediaUrl('image') ?: asset('assets_site/img/medc.jpg') }}"
                                     alt="{{ $item->title }}"
                                     class="rounded"
                                     style="width:44px;height:44px;object-fit:cover;">
                            </td>
                            <td class="fw-semibold">{{ Str::limit($item->title, 55) }}</td>
                            <td><span class="badge bg-primary bg-opacity-10 text-primary">{{ $item->category->title ?? '—' }}</span></td>
                            <td class="text-muted small">{{ $item->user->name ?? '—' }}</td>
                            <td class="text-muted small">{{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}</td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark">{{ $item->commentaires->count() }}</span>
                            </td>
                            <td>
                                <a href="{{ route('post.edit', $item->slug) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">Aucun article.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</section>

<style>
.kpi-card {
    border-radius: 14px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,.07);
}
.kpi-blue   { background: linear-gradient(135deg,#0066CC,#3399FF); color:#fff; }
.kpi-green  { background: linear-gradient(135deg,#00A86B,#2ECC71); color:#fff; }
.kpi-teal   { background: linear-gradient(135deg,#17a2b8,#20c9e0); color:#fff; }
.kpi-purple { background: linear-gradient(135deg,#6f42c1,#9a6dd7); color:#fff; }

.kpi-icon {
    width: 52px; height: 52px;
    border-radius: 12px;
    background: rgba(255,255,255,.25);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.6rem;
    flex-shrink: 0;
}
.kpi-label  { font-size: .75rem; margin-bottom: 2px; opacity: .85; }
.kpi-value  { font-size: 1.7rem; font-weight: 700; margin: 0; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const catCtx = document.getElementById('categoryChart');
    if (catCtx) {
        new Chart(catCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($posts_by_category->pluck('title')) !!},
                datasets: [{ data: {!! json_encode($posts_by_category->pluck('posts_count')) !!},
                    backgroundColor: ['#0066CC','#00A86B','#17a2b8','#6f42c1','#fd7e14','#e83e8c','#20c997','#ffc107'],
                    borderWidth: 2, borderColor: '#fff'
                }]
            },
            options: { responsive: true, plugins: { legend: { display: false } }, cutout: '65%' }
        });
    }

    const pubCtx = document.getElementById('publicationsChart');
    if (pubCtx) {
        new Chart(pubCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($last_30_days_stats->pluck('date')) !!},
                datasets: [{
                    label: 'Publications',
                    data: {!! json_encode($last_30_days_stats->pluck('count')) !!},
                    backgroundColor: 'rgba(0,102,204,.7)',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } }, x: { ticks: { maxTicksLimit: 10 } } }
            }
        });
    }
});
</script>
@endsection
