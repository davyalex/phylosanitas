@extends('admin.layout')
@section('title', 'Maladies de A à Z')

@section('page_header')
<div class="page-bar">
    <div>
        <h1 class="page-bar__title">Maladies de A à Z</h1>
        <p class="page-bar__sub">Une fiche courte et fiable par maladie : symptômes, prévention, quand consulter. Ces pages sont très recherchées sur Google.</p>
    </div>
    <div class="page-bar__actions">
        <a href="{{ route('maladies.index') }}" target="_blank" rel="noopener" class="btn btn-outline-secondary"><i class="bi bi-box-arrow-up-right me-1"></i>Voir sur le site</a>
        <a href="{{ route('maladie.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Nouvelle fiche</a>
    </div>
</div>
@endsection

@section('content')
@php $brouillons = $maladies->where('publie', false)->count(); @endphp

@if($brouillons)
    <div class="alert alert-warning d-flex gap-2 align-items-start">
        <i class="bi bi-pencil-square mt-1"></i>
        <div>{{ $brouillons }} fiche{{ $brouillons > 1 ? 's' : '' }} en brouillon. Faites-les relire par un médecin, puis publiez-les avec le bouton <i class="bi bi-play-fill"></i>.</div>
    </div>
@endif

<div class="panel-a">
    <div class="panel-a__head">
        <h2 class="panel-a__title"><i class="bi bi-journal-medical"></i>{{ $maladies->count() }} fiche{{ $maladies->count() > 1 ? 's' : '' }}</h2>
        <form method="GET" class="d-flex gap-2" role="search">
            <input type="search" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Rechercher une maladie…" aria-label="Rechercher une maladie">
        </form>
    </div>

    @if($maladies->isEmpty())
        <div class="empty-admin">
            <i class="bi bi-journal-medical"></i>
            <h3>Aucune fiche</h3>
            <p>Commencez par les maladies les plus recherchées : paludisme, hypertension, diabète, fièvre typhoïde…</p>
            <a href="{{ route('maladie.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Nouvelle fiche</a>
        </div>
    @else
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">Maladie</th>
                        <th>Statut</th>
                        <th class="table-admin-hide">Relue par</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($maladies as $m)
                        <tr>
                            <td class="ps-3">
                                <a href="{{ route('maladie.edit', $m) }}" class="fw-semibold text-decoration-none">{{ $m->nom }}</a>
                                @if($m->autres_noms)<div class="small text-muted">{{ $m->autres_noms }}</div>@endif
                            </td>
                            <td>
                                @if($m->publie)
                                    <span class="status status--live">Publiée</span>
                                @else
                                    <span class="status status--off">Brouillon</span>
                                @endif
                            </td>
                            <td class="small text-muted table-admin-hide">{{ $m->reviewer->name ?? '—' }}</td>
                            <td class="pe-3">
                                <div class="row-actions">
                                    <form action="{{ route('maladie.toggle', $m) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm {{ $m->publie ? 'btn-outline-warning' : 'btn-outline-success' }} icon-action" title="{{ $m->publie ? 'Repasser en brouillon' : 'Publier' }}" aria-label="{{ $m->publie ? 'Repasser en brouillon' : 'Publier' }}"><i class="bi bi-{{ $m->publie ? 'pause-fill' : 'play-fill' }}"></i></button>
                                    </form>
                                    @if($m->publie)
                                        <a href="{{ route('maladies.show', $m->slug) }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary icon-action" title="Voir sur le site" aria-label="Voir sur le site"><i class="bi bi-eye"></i></a>
                                    @endif
                                    <a href="{{ route('maladie.edit', $m) }}" class="btn btn-sm btn-outline-primary icon-action" title="Modifier" aria-label="Modifier"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('maladie.destroy', $m) }}" method="POST" data-confirm="Supprimer la fiche « {{ $m->nom }} » ?">
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
