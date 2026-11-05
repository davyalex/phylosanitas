@extends('admin.layout')
@section('title', 'Dossiers')

@section('page_header')
<div class="page-bar">
    <div>
        <h1 class="page-bar__title">Dossiers thématiques</h1>
        <p class="page-bar__sub">Regroupez plusieurs articles en un parcours de lecture (ex. « Tout comprendre sur l'hypertension »). Le lecteur passe d'un article au suivant sans chercher.</p>
    </div>
    <div class="page-bar__actions">
        <a href="{{ route('dossiers.index') }}" target="_blank" rel="noopener" class="btn btn-outline-secondary"><i class="bi bi-box-arrow-up-right me-1"></i>Voir sur le site</a>
        <a href="{{ route('dossier.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Nouveau dossier</a>
    </div>
</div>
@endsection

@section('content')
<div class="panel-a">
    @if($dossiers->isEmpty())
        <div class="empty-admin">
            <i class="bi bi-collection-play"></i>
            <h3>Aucun dossier</h3>
            <p>Choisissez un thème sur lequel vous avez déjà plusieurs articles, puis rangez-les dans l'ordre de lecture.</p>
            <a href="{{ route('dossier.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Nouveau dossier</a>
        </div>
    @else
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">Dossier</th>
                        <th>Statut</th>
                        <th class="text-end table-admin-hide">Articles</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dossiers as $d)
                        <tr>
                            <td class="ps-3">
                                <a href="{{ route('dossier.edit', $d) }}" class="fw-semibold text-decoration-none">{{ $d->titre }}</a>
                                @if($d->intro)<div class="small text-muted">{{ Str::limit($d->intro, 90) }}</div>@endif
                            </td>
                            <td>
                                @if($d->actif)
                                    <span class="status status--live">En ligne</span>
                                @else
                                    <span class="status status--off">Masqué</span>
                                @endif
                            </td>
                            <td class="text-end table-admin-hide">{{ $d->posts_count }}</td>
                            <td class="pe-3">
                                <div class="row-actions">
                                    <form action="{{ route('dossier.toggle', $d) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm {{ $d->actif ? 'btn-outline-warning' : 'btn-outline-success' }} icon-action" title="{{ $d->actif ? 'Masquer' : 'Mettre en ligne' }}" aria-label="{{ $d->actif ? 'Masquer' : 'Mettre en ligne' }}"><i class="bi bi-{{ $d->actif ? 'pause-fill' : 'play-fill' }}"></i></button>
                                    </form>
                                    @if($d->actif)
                                        <a href="{{ route('dossiers.show', $d->slug) }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary icon-action" title="Voir sur le site" aria-label="Voir sur le site"><i class="bi bi-eye"></i></a>
                                    @endif
                                    <a href="{{ route('dossier.edit', $d) }}" class="btn btn-sm btn-outline-primary icon-action" title="Modifier" aria-label="Modifier"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('dossier.destroy', $d) }}" method="POST" data-confirm="Supprimer le dossier « {{ $d->titre }} » ? Les articles ne sont pas supprimés.">
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
