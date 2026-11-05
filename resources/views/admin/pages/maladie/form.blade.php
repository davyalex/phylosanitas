@extends('admin.layout')
@section('title', $maladie->exists ? 'Modifier la fiche' : 'Nouvelle fiche maladie')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('maladie.index') }}">Maladies de A à Z</a></li>@endsection

@section('page_header')
<div class="page-bar">
    <div>
        <h1 class="page-bar__title">{{ $maladie->exists ? $maladie->nom : 'Nouvelle fiche maladie' }}</h1>
        <p class="page-bar__sub">Écrivez simplement, pour tout le monde. Dans les listes, mettez <strong>un élément par ligne</strong> : ils s'affichent en puces sur le site.</p>
    </div>
</div>
@endsection

@section('content')
<form action="{{ $maladie->exists ? route('maladie.update', $maladie) : route('maladie.store') }}" method="POST" data-once>
    @csrf
    @if($maladie->exists) @method('PUT') @endif

    <div class="form-shell">
        <div class="d-flex flex-column gap-3">
            <section class="fsec">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="ml-nom" class="form-label">Nom de la maladie <span class="req">*</span></label>
                        <input id="ml-nom" name="nom" value="{{ old('nom', $maladie->nom) }}" class="form-control form-control-lg @error('nom') is-invalid @enderror" maxlength="150" required placeholder="Ex. : Fièvre typhoïde">
                        @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="ml-autres" class="form-label">Autres noms</label>
                        <input id="ml-autres" name="autres_noms" value="{{ old('autres_noms', $maladie->autres_noms) }}" class="form-control" maxlength="255" placeholder="Ex. : typhoïde, salmonellose">
                        <div class="form-text">Aident les lecteurs à trouver la fiche dans la recherche.</div>
                    </div>
                </div>

                <label for="ml-resume" class="form-label mt-3">En résumé <span class="req">*</span> <span class="field-count" data-count-for="ml-resume"></span></label>
                <textarea id="ml-resume" name="resume" rows="3" data-count="800" maxlength="800" required class="form-control @error('resume') is-invalid @enderror" placeholder="Qu'est-ce que c'est, en 2 ou 3 phrases simples ?">{{ old('resume', $maladie->resume) }}</textarea>
                @error('resume')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </section>

            @foreach(\App\Models\Maladie::RUBRIQUES as $champ => [$titre, $icone])
                <section class="fsec">
                    <label for="ml-{{ $champ }}" class="fsec__title"><i class="bi bi-{{ $icone }}"></i>{{ $titre }}</label>
                    <textarea id="ml-{{ $champ }}" name="{{ $champ }}" rows="{{ $champ === 'traitement' ? 3 : 4 }}" maxlength="3000"
                              class="form-control @error($champ) is-invalid @enderror"
                              placeholder="{{ $champ === 'traitement' ? 'Texte libre. Rappelez qu\'il faut un avis médical.' : 'Un élément par ligne' }}">{{ old($champ, $maladie->$champ) }}</textarea>
                    @error($champ)<div class="invalid-feedback">{{ $message }}</div>@enderror
                </section>
            @endforeach
        </div>

        <aside class="form-side">
            <section class="fsec">
                <h2 class="fsec__title"><i class="bi bi-send"></i>Publication</h2>
                <div class="switch-row">
                    <div class="switch-row__text"><strong>Publiée</strong><span>Sinon, la fiche reste en brouillon</span></div>
                    <input class="form-check-input" type="checkbox" role="switch" name="publie" value="1" {{ old('publie', $maladie->publie) ? 'checked' : '' }} aria-label="Publiée">
                </div>
                <label for="ml-reviewer" class="form-label mt-2">Relue par</label>
                <select id="ml-reviewer" name="reviewer_id" class="form-select">
                    <option value="">— Non indiqué —</option>
                    @foreach($medecins as $u)
                        <option value="{{ $u->id }}" {{ (string) old('reviewer_id', $maladie->reviewer_id) === (string) $u->id ? 'selected' : '' }}>{{ $u->est_medecin ? '🩺 ' : '' }}{{ $u->name }}{{ $u->titre_pro ? ' — ' . $u->titre_pro : '' }}</option>
                    @endforeach
                </select>
            </section>

            <section class="fsec">
                <h2 class="fsec__title"><i class="bi bi-link-45deg"></i>Articles liés</h2>
                <label for="ml-mots" class="form-label">Mots-clés</label>
                <input id="ml-mots" name="mots_cles" value="{{ old('mots_cles', $maladie->mots_cles) }}" class="form-control" maxlength="255" placeholder="paludisme, palu, moustiquaire">
                <div class="form-text">Séparés par des virgules. Les articles dont le titre contient un de ces mots s'affichent sous la fiche. Vide = le nom de la maladie.</div>
            </section>
        </aside>
    </div>

    <div class="form-footer">
        <a href="{{ route('maladie.index') }}" class="btn btn-outline-secondary">Annuler</a>
        <div class="form-footer__end">
            @if($maladie->exists && $maladie->publie)
                <a href="{{ route('maladies.show', $maladie->slug) }}" target="_blank" rel="noopener" class="btn btn-link"><i class="bi bi-box-arrow-up-right me-1"></i>Voir sur le site</a>
            @endif
            <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-check-lg me-1"></i>{{ $maladie->exists ? 'Enregistrer' : 'Créer la fiche' }}</button>
        </div>
    </div>
</form>
@endsection
