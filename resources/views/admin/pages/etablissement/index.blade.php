@extends('admin.layout')
@section('title', 'Pharmacies de garde')

@section('page_header')
<div class="page-bar">
    <div>
        <h1 class="page-bar__title">Pharmacies de garde</h1>
        <p class="page-bar__sub">Les pharmacies et leurs périodes de garde. <strong>Mettez à jour les pharmacies de garde chaque semaine</strong> : une information périmée fait perdre la confiance des lecteurs.</p>
    </div>
    <div class="page-bar__actions">
        <a href="{{ route('garde') }}" target="_blank" rel="noopener" class="btn btn-outline-secondary"><i class="bi bi-box-arrow-up-right me-1"></i>Voir sur le site</a>
        <a href="{{ route('etablissement.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Ajouter une pharmacie</a>
    </div>
</div>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-xl-8 order-2 order-xl-1">
        <div class="panel-a">
            <div class="panel-a__head">
                @php $nb = $etablissements->total(); @endphp
                <h2 class="panel-a__title"><i class="bi bi-capsule"></i>{{ $nb }} pharmacie{{ $nb > 1 ? 's' : '' }}</h2>
                <form method="GET" role="search">
                    <input type="search" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Rechercher un nom…" aria-label="Rechercher un nom">
                </form>
            </div>

            @if($etablissements->isEmpty())
                <div class="empty-admin">
                    <i class="bi bi-capsule"></i>
                    <h3>Aucune pharmacie</h3>
                    <p>Le premier import de la garde crée les pharmacies automatiquement. Vous pouvez aussi les ajouter à la main.</p>
                    <a href="{{ route('etablissement.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Ajouter</a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-3">Nom</th>
                                <th class="table-admin-hide">Ville</th>
                                <th class="table-admin-hide">Téléphone</th>
                                <th class="text-end pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($etablissements as $e)
                                <tr class="{{ $e->actif ? '' : 'opacity-50' }}">
                                    <td class="ps-3">
                                        <a href="{{ route('etablissement.edit', $e) }}" class="fw-semibold text-decoration-none">{{ $e->nom }}</a>
                                        @if($e->estDeGarde())<span class="status status--live ms-1">De garde jusqu'au {{ $e->garde_fin->format('d/m') }}</span>@endif
                                        @if($e->quartier)<div class="small text-muted">{{ $e->quartier }}</div>@endif
                                    </td>
                                    <td class="small table-admin-hide">{{ $e->ville ?: '—' }}</td>
                                    <td class="small table-admin-hide text-nowrap">{{ $e->telephone ?: '—' }}</td>
                                    <td class="pe-3">
                                        <div class="row-actions">
                                            @if($e->estDeGarde())
                                                <form action="{{ route('etablissement.fin-garde', $e) }}" method="POST"
                                                      data-confirm="Retirer « {{ $e->nom }} » des pharmacies de garde ? Elle ne sera plus affichée sur la page des gardes." data-confirm-title="Retirer la garde" data-confirm-ok="Retirer">
                                                    @csrf @method('PATCH')
                                                    <button class="btn btn-sm btn-outline-secondary icon-action" title="Retirer la garde" aria-label="Retirer la garde"><i class="bi bi-moon"></i></button>
                                                </form>
                                            @endif
                                            <form action="{{ route('etablissement.toggle', $e) }}" method="POST"
                                                  @if($e->actif) data-confirm="Masquer « {{ $e->nom }} » ? Elle n'apparaîtra plus sur le site, même si elle est de garde." data-confirm-title="Masquer la pharmacie" data-confirm-ok="Masquer" @endif>
                                                @csrf @method('PATCH')
                                                <button class="btn btn-sm {{ $e->actif ? 'btn-outline-warning' : 'btn-outline-success' }} icon-action" title="{{ $e->actif ? 'Masquer' : 'Afficher' }}" aria-label="{{ $e->actif ? 'Masquer' : 'Afficher' }}"><i class="bi bi-{{ $e->actif ? 'eye-slash' : 'eye' }}"></i></button>
                                            </form>
                                            <a href="{{ route('etablissement.edit', $e) }}" class="btn btn-sm btn-outline-primary icon-action" title="Modifier" aria-label="Modifier"><i class="bi bi-pencil"></i></a>
                                            <form action="{{ route('etablissement.destroy', $e) }}" method="POST" data-confirm="Supprimer « {{ $e->nom }} » ?">
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
                @if($etablissements->hasPages())<div class="p-3">{{ $etablissements->links('pagination::bootstrap-5') }}</div>@endif
            @endif
        </div>
    </div>

    {{-- Garde de la semaine --}}
    <div class="col-xl-4 order-1 order-xl-2">
        {{-- Import de la liste de la semaine (fichier envoyé depuis l'ordinateur) --}}
        @php $imp = \App\Services\GardesImport::dernier(); @endphp
        <section class="fsec mb-3">
            <h2 class="fsec__title"><i class="bi bi-upload"></i>Importer la liste de la semaine</h2>

            @if($imp)
                <div class="alert {{ $imp['ok'] ? 'alert-success' : 'alert-danger' }} small py-2 mb-3">
                    <strong>{{ $imp['ok'] ? 'Dernier import réussi' : 'Dernier import refusé' }}</strong>
                    · {{ \Carbon\Carbon::parse($imp['at'])->diffForHumans() }}<br>
                    @if(!empty($imp['fichier']))<span class="text-break">Fichier : « {{ $imp['fichier'] }} »</span><br>@endif
                    {{ $imp['message'] }}
                    @if($imp['ok'] && $imp['debut'])<br>Garde du {{ \Carbon\Carbon::parse($imp['debut'])->format('d/m') }} au {{ \Carbon\Carbon::parse($imp['fin'])->format('d/m/Y') }}.@endif
                </div>
            @endif

            <form action="{{ route('etablissement.import.fichier') }}" method="POST" enctype="multipart/form-data" data-once>
                @csrf
                <label for="imp-fichier" class="form-label small">Fichier des pharmacies de garde</label>
                <input id="imp-fichier" type="file" name="fichier" accept=".csv,.xlsx,.json,.txt" class="form-control form-control-sm @error('fichier') is-invalid @enderror" required>
                @error('fichier')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">CSV, Excel (.xlsx) ou JSON. La période est lue dans le nom du fichier, ex. <code>…_2026-10-31_au_11-06.csv</code>.</div>
                <button type="submit" class="btn btn-primary btn-sm w-100 mt-2"><i class="bi bi-upload me-1"></i>Importer et publier</button>
            </form>
        </section>

        <form action="{{ route('etablissement.garde') }}" method="POST" class="fsec" id="g-form" data-once data-confirm-title="Retirer des gardes" data-confirm-ok="Enregistrer quand même">
            @csrf
            <h2 class="fsec__title"><i class="bi bi-moon-stars"></i>Pharmacies de garde</h2>
            <p class="small text-muted"><a href="{{ route('garde') }}" target="_blank" rel="noopener">{{ $nbGarde }} pharmacie{{ $nbGarde > 1 ? 's' : '' }} de garde aujourd'hui <i class="bi bi-box-arrow-up-right"></i></a>. Cochez celles de la prochaine période puis enregistrez.</p>

            @if($pharmacies->isEmpty())
                <p class="small">Ajoutez d'abord des pharmacies, ou importez la liste de la semaine ci-dessus.</p>
            @else
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label small" for="g-debut">Du</label>
                        <input id="g-debut" type="date" name="garde_debut" value="{{ old('garde_debut', now()->toDateString()) }}" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small" for="g-fin">Au</label>
                        <input id="g-fin" type="date" name="garde_fin" value="{{ old('garde_fin', now()->addDays(6)->toDateString()) }}" class="form-control form-control-sm @error('garde_fin') is-invalid @enderror" required>
                    </div>
                </div>

                <input type="search" class="form-control form-control-sm mb-2" placeholder="Filtrer…" aria-label="Filtrer les pharmacies" id="g-filtre">
                <div class="border rounded p-2 mb-2" style="max-height:300px;overflow:auto" id="g-liste">
                    @foreach($pharmacies as $p)
                        @php $deGarde = $p->estDeGarde(); @endphp
                        <label class="d-flex align-items-start gap-2 py-1 small" data-nom="{{ Str::lower($p->nom . ' ' . $p->ville . ' ' . $p->quartier) }}">
                            <input type="checkbox" class="form-check-input mt-1" name="pharmacies[]" value="{{ $p->id }}" @if($deGarde) data-garde @endif {{ in_array($p->id, old('pharmacies', [])) ? 'checked' : '' }}>
                            <span>{{ $p->nom }} <span class="text-muted">{{ collect([$p->quartier, $p->ville])->filter()->implode(', ') }}</span>
                                @if($deGarde)<span class="badge text-bg-success">de garde</span>@endif</span>
                        </label>
                    @endforeach
                </div>
                @error('pharmacies')<div class="text-danger small mb-2">{{ $message }}</div>@enderror

                <label class="d-flex align-items-start gap-2 small mb-3">
                    <input type="checkbox" class="form-check-input mt-1" name="remplacer" value="1" checked>
                    <span>Retirer la garde des pharmacies non cochées</span>
                </label>
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-check-lg me-1"></i>Enregistrer la garde</button>
            @endif
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Confirmation quand l'enregistrement retire la garde de pharmacies actuellement de garde
    (function () {
        var form = document.getElementById('g-form');
        var remplacer = form && form.querySelector('[name="remplacer"]');
        if (!remplacer) return;
        function maj() {
            var n = remplacer.checked ? form.querySelectorAll('input[name="pharmacies[]"][data-garde]:not(:checked)').length : 0;
            if (n) form.setAttribute('data-confirm', n + (n > 1 ? ' pharmacies actuellement de garde ne sont pas cochées : elles ne seront plus de garde.' : ' pharmacie actuellement de garde n\'est pas cochée : elle ne sera plus de garde.'));
            else form.removeAttribute('data-confirm');
        }
        form.addEventListener('change', maj);
        maj();
    })();

    (function () {
        var f = document.getElementById('g-filtre');
        if (!f) return;
        f.addEventListener('input', function () {
            var q = f.value.trim().toLowerCase();
            document.querySelectorAll('#g-liste [data-nom]').forEach(function (l) {
                l.classList.toggle('d-none', q && l.dataset.nom.indexOf(q) === -1);
            });
        });
    })();
</script>
@endpush
