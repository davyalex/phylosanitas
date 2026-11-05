@extends('admin.layout')
@php $ajouter = 'Ajouter une pharmacie'; @endphp
@section('title', $etab->exists ? 'Modifier' : $ajouter)
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('etablissement.index') }}">Pharmacies de garde</a></li>@endsection

@section('page_header')
<div class="page-bar">
    <div>
        <h1 class="page-bar__title">{{ $etab->exists ? $etab->nom : $ajouter }}</h1>
        <p class="page-bar__sub">Vérifiez le numéro de téléphone : c'est l'information que les lecteurs utilisent le plus.</p>
    </div>
</div>
@endsection

@section('content')
<form action="{{ $etab->exists ? route('etablissement.update', $etab) : route('etablissement.store') }}" method="POST" data-once>
    @csrf
    @if($etab->exists) @method('PUT') @endif

    <div class="form-shell">
        <div class="d-flex flex-column gap-3">
            <section class="fsec">
                <label for="et-nom" class="form-label">Nom <span class="req">*</span></label>
                <input id="et-nom" name="nom" value="{{ old('nom', $etab->nom) }}" class="form-control form-control-lg @error('nom') is-invalid @enderror" maxlength="150" required placeholder="Ex. : Pharmacie du Plateau">
                @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror

                <div class="row g-3 mt-0">
                    <div class="col-md-6">
                        <label for="et-tel" class="form-label">Téléphone</label>
                        <input id="et-tel" type="tel" name="telephone" value="{{ old('telephone', $etab->telephone) }}" class="form-control @error('telephone') is-invalid @enderror" maxlength="40" placeholder="27 20 00 00 00">
                        @error('telephone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="et-tel2" class="form-label">Second téléphone</label>
                        <input id="et-tel2" type="tel" name="telephone_2" value="{{ old('telephone_2', $etab->telephone_2) }}" class="form-control" maxlength="40">
                    </div>
                </div>
            </section>

            <section class="fsec">
                <h2 class="fsec__title"><i class="bi bi-geo-alt"></i>Adresse</h2>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="et-ville" class="form-label">Ville</label>
                        <input id="et-ville" name="ville" value="{{ old('ville', $etab->ville) }}" class="form-control" maxlength="100" placeholder="Abidjan" list="et-villes">
                    </div>
                    <div class="col-md-6">
                        <label for="et-quartier" class="form-label">Commune / quartier</label>
                        <input id="et-quartier" name="quartier" value="{{ old('quartier', $etab->quartier) }}" class="form-control" maxlength="100" placeholder="Cocody, Riviera 2">
                    </div>
                    <div class="col-12">
                        <label for="et-adresse" class="form-label">Repère / adresse</label>
                        <input id="et-adresse" name="adresse" value="{{ old('adresse', $etab->adresse) }}" class="form-control" maxlength="255" placeholder="Face à la station, près du carrefour…">
                    </div>
                    <div class="col-12">
                        <label for="et-maps" class="form-label">Lien Google Maps</label>
                        <input id="et-maps" type="url" name="maps_url" value="{{ old('maps_url', $etab->maps_url) }}" class="form-control @error('maps_url') is-invalid @enderror" maxlength="500" placeholder="https://maps.app.goo.gl/…">
                        <div class="form-text">Facultatif. Sans lien, le site utilise les coordonnées GPS ci-dessous, sinon une recherche Google Maps sur le nom et l'adresse.</div>
                        @error('maps_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="et-lat" class="form-label">Latitude</label>
                        <input id="et-lat" name="latitude" value="{{ old('latitude', $etab->latitude) }}" class="form-control @error('latitude') is-invalid @enderror" inputmode="decimal" placeholder="5.35438">
                        @error('latitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="et-lng" class="form-label">Longitude</label>
                        <input id="et-lng" name="longitude" value="{{ old('longitude', $etab->longitude) }}" class="form-control @error('longitude') is-invalid @enderror" inputmode="decimal" placeholder="-4.03046">
                        @error('longitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </section>

            <section class="fsec">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="et-horaires" class="form-label">Horaires</label>
                        <input id="et-horaires" name="horaires" value="{{ old('horaires', $etab->horaires) }}" class="form-control" maxlength="150" placeholder="Lun-Sam 8h-20h · 24 h/24">
                    </div>
                    <div class="col-md-6">
                        <label for="et-notes" class="form-label">Précision</label>
                        <input id="et-notes" name="notes" value="{{ old('notes', $etab->notes) }}" class="form-control" maxlength="255" placeholder="Ouverte 24 h/24, accès par la rue…">
                    </div>
                </div>
            </section>
        </div>

        <aside class="form-side">
            <section class="fsec">
                <h2 class="fsec__title"><i class="bi bi-eye"></i>Affichage</h2>
                <div class="switch-row">
                    <div class="switch-row__text"><strong>Visible sur le site</strong><span>Désactivez si la pharmacie a fermé</span></div>
                    <input class="form-check-input" type="checkbox" role="switch" name="actif" value="1" {{ old('actif', $etab->actif ?? true) ? 'checked' : '' }} aria-label="Visible sur le site">
                </div>
            </section>

            <section class="fsec">
                <h2 class="fsec__title"><i class="bi bi-moon-stars"></i>Garde</h2>
                <div class="mb-2">
                    <label for="et-gd" class="form-label">Du</label>
                    <input id="et-gd" type="date" name="garde_debut" value="{{ old('garde_debut', optional($etab->garde_debut)->format('Y-m-d')) }}" class="form-control">
                </div>
                <label for="et-gf" class="form-label">Au</label>
                <input id="et-gf" type="date" name="garde_fin" value="{{ old('garde_fin', optional($etab->garde_fin)->format('Y-m-d')) }}" class="form-control @error('garde_fin') is-invalid @enderror">
                @error('garde_fin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">Pour plusieurs pharmacies d'un coup, utilisez le bloc « Pharmacies de garde » de la liste.</div>
            </section>
        </aside>
    </div>

    <div class="form-footer">
        <a href="{{ route('etablissement.index') }}" class="btn btn-outline-secondary">Annuler</a>
        <div class="form-footer__end">
            @unless($etab->exists)
                <button type="submit" name="encore" value="1" class="btn btn-outline-primary">Enregistrer et ajouter un autre</button>
            @endunless
            <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-check-lg me-1"></i>Enregistrer</button>
        </div>
    </div>
</form>
@endsection
