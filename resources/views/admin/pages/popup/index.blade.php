@extends('admin.layout')
@section('title', 'Popups')

@section('page_header')
<div class="page-bar">
    <div>
        <h1 class="page-bar__title">Popups d'information</h1>
        <p class="page-bar__sub">Une affiche ou une annonce qui s'ouvre à l'arrivée sur le site (ex. campagne « Novembre bleu »). Un seul popup s'affiche à la fois : le plus récent en diffusion.</p>
    </div>
    <div class="page-bar__actions">
        <a href="{{ route('popup.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Nouveau popup</a>
    </div>
</div>
@endsection

@section('content')
@php
    $statusLabels = ['live' => 'En diffusion', 'scheduled' => 'Programmé', 'expired' => 'Terminé', 'off' => 'En pause'];
@endphp

@if($enLigne)
    <div class="alert alert-success d-flex align-items-center gap-2 mb-3" role="status">
        <i class="bi bi-broadcast"></i>
        <div>Affiché en ce moment sur le site : <strong>{{ $enLigne->titre }}</strong>
            · {{ \App\Models\Popup::PAGES[$enLigne->pages]['label'] }}
            · {{ mb_strtolower(\App\Models\Popup::FREQUENCES[$enLigne->frequence]['label']) }}</div>
    </div>
@endif

<div class="panel-a">
    @if($popups->isEmpty())
        <div class="empty-admin">
            <i class="bi bi-window-stack"></i>
            <h3>Aucun popup</h3>
            <p>Créez votre premier popup : une affiche de campagne, une annonce de journée de dépistage…</p>
            <a href="{{ route('popup.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Nouveau popup</a>
        </div>
    @else
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">Aperçu</th>
                        <th>Popup</th>
                        <th>Statut</th>
                        <th class="table-admin-hide">Période</th>
                        <th class="text-end">Performance</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($popups as $p)
                        @php $img = $p->imageUrl(); @endphp
                        <tr>
                            <td class="ps-3">
                                @if($img)
                                    <img src="{{ $img }}" alt="" class="popup-thumb" loading="lazy">
                                @else
                                    <span class="popup-thumb popup-thumb--text"><i class="bi bi-card-text"></i></span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('popup.edit', $p) }}" class="fw-semibold text-decoration-none">{{ $p->titre }}</a>
                                @if($enLigne && $enLigne->id === $p->id)
                                    <span class="badge rounded-pill text-bg-success ms-1">Sur le site</span>
                                @endif
                                <div class="small text-muted">
                                    {{ \App\Models\Popup::PAGES[$p->pages]['label'] ?? $p->pages }}
                                    · {{ \App\Models\Popup::FREQUENCES[$p->frequence]['label'] ?? $p->frequence }}
                                    · après {{ $p->delai }}&nbsp;s
                                </div>
                            </td>
                            <td><span class="status status--{{ $p->statut }}">{{ $statusLabels[$p->statut] }}</span></td>
                            <td class="small text-muted table-admin-hide">
                                {{ $p->debut_at ? 'Du ' . $p->debut_at->format('d/m/Y') : 'Dès maintenant' }}<br>
                                {{ $p->fin_at ? 'au ' . $p->fin_at->format('d/m/Y') : 'Sans fin' }}
                            </td>
                            <td class="text-end ad-stats">
                                {{ number_format($p->vues, 0, ',', ' ') }} <span>affichages</span><br>
                                {{ number_format($p->clics, 0, ',', ' ') }} <span>clics{{ $p->lien ? ' · ' . number_format($p->ctr, 1, ',', ' ') . ' %' : '' }}</span>
                            </td>
                            <td class="pe-3">
                                <div class="row-actions">
                                    <form action="{{ route('popup.toggle', $p) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm {{ $p->actif ? 'btn-outline-warning' : 'btn-outline-success' }} icon-action" title="{{ $p->actif ? 'Mettre en pause' : 'Activer' }}" aria-label="{{ $p->actif ? 'Mettre en pause' : 'Activer' }}"><i class="bi bi-{{ $p->actif ? 'pause-fill' : 'play-fill' }}"></i></button>
                                    </form>
                                    <a href="{{ route('popup.edit', $p) }}" class="btn btn-sm btn-outline-primary icon-action" title="Modifier" aria-label="Modifier"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('popup.destroy', $p) }}" method="POST" data-confirm="Supprimer le popup « {{ $p->titre }} » ?">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger icon-action" title="Supprimer" aria-label="Supprimer"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
