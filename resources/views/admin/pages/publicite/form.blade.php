@extends('admin.layout')
@section('title', $pub->exists ? 'Modifier la publicité' : 'Nouvelle publicité')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('publicite.index') }}">Publicités</a></li>@endsection

@section('page_header')
<div class="page-bar">
    <div>
        <h1 class="page-bar__title">{{ $pub->exists ? 'Modifier « ' . $pub->titre . ' »' : 'Nouvelle publicité' }}</h1>
        <p class="page-bar__sub">Choisissez l'emplacement, puis ajoutez une bannière image (avec lien) ou collez un code d'annonce.</p>
    </div>
</div>
@endsection

@section('content')
@php
    $current = $pub->exists ? $pub->imageUrl() : null;
    $empl    = old('emplacement', $pub->emplacement ?? 'leaderboard');
    $type    = old('type', $pub->type ?? 'image');
@endphp

<form action="{{ $pub->exists ? route('publicite.update', $pub) : route('publicite.store') }}" method="POST" enctype="multipart/form-data" data-once>
    @csrf
    @if($pub->exists) @method('PUT') @endif

    <div class="form-shell">
        <div class="d-flex flex-column gap-3">

            <section class="fsec">
                <h2 class="fsec__title"><i class="bi bi-info-circle"></i>Informations</h2>
                <div class="row g-3">
                    <div class="col-md-7">
                        <label for="ad-titre" class="form-label">Nom de la publicité <span class="req">*</span></label>
                        <input id="ad-titre" name="titre" value="{{ old('titre', $pub->titre) }}" class="form-control @error('titre') is-invalid @enderror" maxlength="150" required placeholder="Ex. : Pharmacie du Plateau — campagne juin">
                        <div class="form-text">Visible uniquement dans l'administration (et comme texte alternatif de l'image).</div>
                        @error('titre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-5">
                        <label for="ad-annonceur" class="form-label">Annonceur</label>
                        <input id="ad-annonceur" name="annonceur" value="{{ old('annonceur', $pub->annonceur) }}" class="form-control" maxlength="150" placeholder="Nom du partenaire">
                    </div>
                </div>
            </section>

            <section class="fsec">
                <h2 class="fsec__title"><i class="bi bi-layout-text-window"></i>Emplacement <span class="req">*</span></h2>
                <div class="seg" style="grid-auto-flow: row; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
                    @foreach(\App\Models\Publicite::EMPLACEMENTS as $key => $e)
                        <input type="radio" name="emplacement" id="emp-{{ $key }}" value="{{ $key }}" {{ $empl === $key ? 'checked' : '' }} required>
                        <label for="emp-{{ $key }}">
                            <strong>{{ $e['label'] }}</strong>
                            <span>{{ $e['taille'] }}</span>
                            <span>{{ $e['pages'] }}</span>
                        </label>
                    @endforeach
                </div>
            </section>

            <section class="fsec">
                <h2 class="fsec__title"><i class="bi bi-image"></i>Contenu</h2>
                <div class="seg mb-3">
                    <input type="radio" name="type" id="type-image" value="image" {{ $type === 'image' ? 'checked' : '' }}>
                    <label for="type-image"><strong><i class="bi bi-image"></i>Bannière image</strong><span>Image + lien vers le partenaire</span></label>
                    <input type="radio" name="type" id="type-code" value="code" {{ $type === 'code' ? 'checked' : '' }}>
                    <label for="type-code"><strong><i class="bi bi-code-slash"></i>Code d'annonce</strong><span>AdSense ou HTML fourni par la régie</span></label>
                </div>

                <div data-show-when="type=image">
                    <label class="form-label">Image de la bannière @if(!$current)<span class="req">*</span>@endif</label>
                    <div class="dropzone {{ $current ? 'has-image' : '' }} @error('image') border-danger @enderror" data-fit="contain" id="ad-dropzone">
                        @if($current)<img src="{{ $current }}" alt="Bannière actuelle" class="dropzone__preview dropzone__preview--contain">@endif
                        <span class="dropzone__empty d-flex flex-column align-items-center gap-1">
                            <i class="bi bi-cloud-arrow-up"></i>
                            <strong>Glissez l'image ici ou cliquez pour choisir</strong>
                            <small>JPG, PNG, WebP ou GIF · 4 Mo max · taille conseillée selon l'emplacement</small>
                        </span>
                        <span class="dropzone__change">Changer l'image</span>
                        <input type="file" name="image" accept="image/*" aria-label="Image de la bannière">
                    </div>
                    @error('image')<div class="text-danger small mt-1">{{ $message }}</div>@enderror

                    <div class="mt-3">
                        <label for="ad-lien" class="form-label">Lien de destination <span class="req">*</span></label>
                        <input id="ad-lien" type="url" name="lien" value="{{ old('lien', $pub->lien) }}" class="form-control @error('lien') is-invalid @enderror" placeholder="https://site-du-partenaire.com">
                        <div class="form-text">Chaque clic est compté avant la redirection.</div>
                        @error('lien')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div data-show-when="type=code">
                    <label for="ad-code" class="form-label">Code de l'annonce <span class="req">*</span></label>
                    <textarea id="ad-code" name="code" rows="7" class="form-control code-input @error('code') is-invalid @enderror" placeholder='<ins class="adsbygoogle" data-ad-client="ca-pub-…" data-ad-slot="…"></ins>&#10;<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>'>{{ old('code', $pub->code) }}</textarea>
                    <div class="form-text">Collez le code tel que fourni. Le script principal AdSense est déjà chargé sur le site.</div>
                    @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </section>

            <section class="fsec" data-show-when="type=image">
                <h2 class="fsec__title"><i class="bi bi-eye"></i>Aperçu</h2>
                <div class="ad-preview">
                    <span class="ad-preview__label">Publicité</span>
                    <div class="ad-preview__box" id="ad-preview-box">
                        @if($current)<img src="{{ $current }}" alt="">@else Ajoutez une image pour voir l'aperçu @endif
                    </div>
                </div>
            </section>
        </div>

        <aside class="form-side">
            <section class="fsec">
                <h2 class="fsec__title"><i class="bi bi-calendar-range"></i>Diffusion</h2>
                <div class="switch-row">
                    <div class="switch-row__text"><strong>Active</strong><span>Désactivez pour mettre en pause sans supprimer</span></div>
                    <input class="form-check-input" type="checkbox" role="switch" name="actif" value="1" {{ old('actif', $pub->actif ?? true) ? 'checked' : '' }} aria-label="Active">
                </div>
                <div class="mb-3 mt-2">
                    <label for="ad-debut" class="form-label">Début</label>
                    <input id="ad-debut" type="date" name="debut_at" value="{{ old('debut_at', optional($pub->debut_at)->format('Y-m-d')) }}" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="ad-fin" class="form-label">Fin</label>
                    <input id="ad-fin" type="date" name="fin_at" value="{{ old('fin_at', optional($pub->fin_at)->format('Y-m-d')) }}" class="form-control @error('fin_at') is-invalid @enderror">
                    <div class="form-text">Vide = sans date de fin. La publicité s'arrête automatiquement.</div>
                    @error('fin_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label for="ad-prio" class="form-label">Priorité (1 à 10)</label>
                    <input id="ad-prio" type="number" min="1" max="10" name="priorite" value="{{ old('priorite', $pub->priorite ?? 1) }}" class="form-control">
                    <div class="form-text">Si plusieurs publicités partagent un emplacement, une priorité de 3 s'affiche 3 fois plus souvent qu'une priorité de 1.</div>
                </div>
            </section>

            @if($pub->exists)
                <section class="fsec">
                    <h2 class="fsec__title"><i class="bi bi-graph-up"></i>Performance</h2>
                    <div class="d-flex justify-content-between mb-1"><span class="text-muted">Impressions</span><strong>{{ number_format($pub->impressions, 0, ',', ' ') }}</strong></div>
                    <div class="d-flex justify-content-between mb-1"><span class="text-muted">Clics</span><strong>{{ number_format($pub->clics, 0, ',', ' ') }}</strong></div>
                    <div class="d-flex justify-content-between"><span class="text-muted">Taux de clic</span><strong>{{ number_format($pub->ctr, 2, ',', ' ') }}&nbsp;%</strong></div>
                </section>
            @endif

        </aside>
    </div>

    <div class="form-footer">
        <a href="{{ route('publicite.index') }}" class="btn btn-outline-secondary">Annuler</a>
        <div class="form-footer__end">
            <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-check-lg me-1"></i>{{ $pub->exists ? 'Enregistrer les modifications' : 'Créer la publicité' }}</button>
        </div>
    </div>
</form>

@if($pub->exists)
    <form action="{{ route('publicite.reset', $pub) }}" method="POST" class="mt-3" data-confirm="Remettre à zéro les impressions et les clics ?">
        @csrf @method('PATCH')
        <button type="submit" class="btn btn-link text-muted p-0 small">Remettre les statistiques à zéro</button>
    </form>
@endif
@endsection

@push('scripts')
<script>
    document.getElementById('ad-dropzone')?.addEventListener('preview', function (e) {
        var box = document.getElementById('ad-preview-box');
        box.innerHTML = '';
        var img = document.createElement('img');
        img.src = e.detail; img.alt = '';
        box.appendChild(img);
    });
</script>
@endpush
