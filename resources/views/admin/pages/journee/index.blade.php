@extends('admin.layout')
@section('title', 'Journées santé')

@section('page_header')
<div class="page-bar">
    <div>
        <h1 class="page-bar__title">Journées et mois de santé</h1>
        <p class="page-bar__sub">Elles reviennent chaque année. Un bandeau s'affiche automatiquement sur le site le jour J (et quelques jours avant), ou tout le mois pour les campagnes comme Octobre rose.</p>
    </div>
    <div class="page-bar__actions">
        <a href="{{ route('calendrier') }}" target="_blank" rel="noopener" class="btn btn-outline-secondary"><i class="bi bi-box-arrow-up-right me-1"></i>Voir le calendrier</a>
    </div>
</div>
@endsection

@section('content')
@if($bandeau)
    <div class="alert d-flex align-items-center gap-2 mb-3 text-white" style="background: {{ $bandeau->couleur }}" role="status">
        <i class="bi bi-megaphone"></i>
        <div>Bandeau affiché en ce moment sur le site : <strong>{{ $bandeau->titre }}</strong>
            @if($bandeau->etat === 'bientot') (dans {{ $bandeau->dans }} jour{{ $bandeau->dans > 1 ? 's' : '' }})@endif</div>
    </div>
@endif

<div class="row g-4">
    <div class="col-xl-4">
        <form action="{{ route('journee.store') }}" method="POST" class="fsec" data-once>
            @csrf
            <h2 class="fsec__title"><i class="bi bi-plus-circle"></i>Nouvelle journée</h2>
            @include('admin.pages.journee._fields', ['j' => null, 'pfx' => 'new'])
            <button type="submit" class="btn btn-primary w-100 mt-3"><i class="bi bi-plus-lg me-1"></i>Ajouter</button>
        </form>
    </div>

    <div class="col-xl-8">
        <div class="panel-a">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-3">Date</th>
                            <th>Journée</th>
                            <th class="table-admin-hide">Annonce</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($journees as $j)
                            <tr class="{{ $j->actif ? '' : 'opacity-50' }}">
                                <td class="ps-3 text-nowrap small fw-semibold">
                                    <span class="d-inline-block rounded-circle me-1" style="width:10px;height:10px;background:{{ $j->couleur }}"></span>
                                    {{ $j->date_libelle }}
                                </td>
                                <td>
                                    <span class="fw-semibold">{{ $j->titre }}</span>
                                    @if($j->description)<div class="small text-muted">{{ Str::limit($j->description, 90) }}</div>@endif
                                </td>
                                <td class="small text-muted table-admin-hide">{{ $j->estMensuelle() ? 'Tout le mois' : $j->annonce_jours . ' j avant' }}</td>
                                <td class="pe-3">
                                    <div class="row-actions">
                                        <form action="{{ route('journee.toggle', $j) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button class="btn btn-sm {{ $j->actif ? 'btn-outline-warning' : 'btn-outline-success' }} icon-action" title="{{ $j->actif ? 'Désactiver' : 'Activer' }}" aria-label="{{ $j->actif ? 'Désactiver' : 'Activer' }}"><i class="bi bi-{{ $j->actif ? 'pause-fill' : 'play-fill' }}"></i></button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-outline-primary icon-action" data-bs-toggle="modal" data-bs-target="#editJ{{ $j->id }}" title="Modifier" aria-label="Modifier"><i class="bi bi-pencil"></i></button>
                                        <form action="{{ route('journee.destroy', $j) }}" method="POST" data-confirm="Supprimer « {{ $j->titre }} » du calendrier ?">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger icon-action" title="Supprimer" aria-label="Supprimer"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>

                                    <div class="modal fade" id="editJ{{ $j->id }}" tabindex="-1" aria-labelledby="editJLabel{{ $j->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <form class="modal-content" action="{{ route('journee.update', $j) }}" method="POST">
                                                @csrf @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editJLabel{{ $j->id }}">Modifier la journée</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                                </div>
                                                <div class="modal-body">
                                                    @include('admin.pages.journee._fields', ['j' => $j, 'pfx' => 'e' . $j->id])
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                                                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // « Tout le mois » désactive le champ jour
    document.querySelectorAll('.js-tout-mois').forEach(function (cb) {
        var jour = document.getElementById(cb.dataset.jour);
        var sync = function () { jour.disabled = cb.checked; if (cb.checked) jour.value = ''; };
        cb.addEventListener('change', sync); sync();
    });
</script>
@endpush
