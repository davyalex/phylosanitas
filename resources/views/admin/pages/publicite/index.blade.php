@extends('admin.layout')
@section('title', 'Publicités')

@section('page_header')
<div class="page-bar">
    <div>
        <h1 class="page-bar__title">Publicités</h1>
        <p class="page-bar__sub">Bannières de vos partenaires et codes AdSense, par emplacement du site. Quand un emplacement n'a aucune publicité active, le site affiche AdSense (si configuré) ou l'encart « Espace partenaire ».</p>
    </div>
    <div class="page-bar__actions">
        <a href="{{ route('publicite.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Nouvelle publicité</a>
    </div>
</div>
@endsection

@section('content')
@php
    $statusLabels = ['live' => 'En diffusion', 'scheduled' => 'Programmée', 'expired' => 'Terminée', 'off' => 'En pause'];
@endphp

<div class="kpis">
    <div class="kpi"><div class="kpi__top"><span class="kpi__label">En diffusion</span><span class="kpi__icon kpi__icon--green"><i class="bi bi-broadcast"></i></span></div><div class="kpi__value">{{ $stats['live'] }}</div></div>
    <div class="kpi"><div class="kpi__top"><span class="kpi__label">Impressions</span><span class="kpi__icon"><i class="bi bi-eye"></i></span></div><div class="kpi__value">{{ number_format($stats['impressions'], 0, ',', ' ') }}</div></div>
    <div class="kpi"><div class="kpi__top"><span class="kpi__label">Clics</span><span class="kpi__icon"><i class="bi bi-cursor"></i></span></div><div class="kpi__value">{{ number_format($stats['clics'], 0, ',', ' ') }}</div></div>
    <div class="kpi"><div class="kpi__top"><span class="kpi__label">Taux de clic moyen</span><span class="kpi__icon kpi__icon--amber"><i class="bi bi-percent"></i></span></div><div class="kpi__value">{{ number_format($stats['ctr'], 2, ',', ' ') }}&nbsp;%</div></div>
</div>

{{-- Emplacements (servent aussi de filtre) --}}
<div class="placements">
    <a href="{{ route('publicite.index') }}" class="placement {{ !$emplacement ? 'is-active' : '' }}">
        <div class="placement__head"><span class="placement__name">Tous les emplacements</span></div>
        <small>Voir toutes les publicités</small>
    </a>
    @foreach(\App\Models\Publicite::EMPLACEMENTS as $key => $e)
        @php $n = $parEmplacement[$key] ?? 0; @endphp
        <a href="{{ route('publicite.index', ['emplacement' => $key]) }}" class="placement {{ $emplacement === $key ? 'is-active' : '' }}">
            <div class="placement__head">
                <span class="placement__name">{{ $e['label'] }}</span>
                <span class="placement__count {{ $n ? '' : 'is-zero' }}">{{ $n }} active{{ $n > 1 ? 's' : '' }}</span>
            </div>
            <small>{{ $e['taille'] }}</small>
            <small>{{ $e['pages'] }}</small>
        </a>
    @endforeach
</div>

<div class="panel-a">
    @if($publicites->isEmpty())
        <div class="empty-admin">
            <i class="bi bi-badge-ad"></i>
            <h3>Aucune publicité {{ $emplacement ? 'pour cet emplacement' : '' }}</h3>
            <p>Ajoutez une bannière partenaire ou un code AdSense.</p>
            <a href="{{ route('publicite.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Nouvelle publicité</a>
        </div>
    @else
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">Aperçu</th>
                        <th>Publicité</th>
                        <th>Statut</th>
                        <th class="table-admin-hide">Période</th>
                        <th class="text-end">Performance</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($publicites as $p)
                        <tr>
                            <td class="ps-3">
                                @if($p->type === 'image' && $p->imageUrl())
                                    <img src="{{ $p->imageUrl() }}" alt="" class="ad-thumb" loading="lazy">
                                @else
                                    <span class="ad-thumb ad-thumb--code">&lt;/&gt; Code</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('publicite.edit', $p) }}" class="fw-semibold text-decoration-none">{{ $p->titre }}</a>
                                <div class="small text-muted">
                                    {{ \App\Models\Publicite::EMPLACEMENTS[$p->emplacement]['label'] ?? $p->emplacement }}
                                    @if($p->annonceur) · {{ $p->annonceur }} @endif
                                    @if($p->priorite > 1) · priorité {{ $p->priorite }} @endif
                                </div>
                            </td>
                            <td><span class="status status--{{ $p->statut }}">{{ $statusLabels[$p->statut] }}</span></td>
                            <td class="small text-muted table-admin-hide">
                                {{ $p->debut_at ? $p->debut_at->format('d/m/Y') : 'Dès maintenant' }}<br>
                                {{ $p->fin_at ? 'au ' . $p->fin_at->format('d/m/Y') : 'Sans fin' }}
                            </td>
                            <td class="text-end ad-stats">
                                {{ number_format($p->impressions, 0, ',', ' ') }} <span>vues</span><br>
                                {{ number_format($p->clics, 0, ',', ' ') }} <span>clics · {{ number_format($p->ctr, 2, ',', ' ') }}&nbsp;%</span>
                            </td>
                            <td class="pe-3">
                                <div class="row-actions">
                                    <form action="{{ route('publicite.toggle', $p) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm {{ $p->actif ? 'btn-outline-warning' : 'btn-outline-success' }} icon-action" title="{{ $p->actif ? 'Mettre en pause' : 'Activer' }}" aria-label="{{ $p->actif ? 'Mettre en pause' : 'Activer' }}"><i class="bi bi-{{ $p->actif ? 'pause-fill' : 'play-fill' }}"></i></button>
                                    </form>
                                    <a href="{{ route('publicite.edit', $p) }}" class="btn btn-sm btn-outline-primary icon-action" title="Modifier" aria-label="Modifier"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('publicite.destroy', $p) }}" method="POST" data-confirm="Supprimer la publicité « {{ $p->titre }} » ?">
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
