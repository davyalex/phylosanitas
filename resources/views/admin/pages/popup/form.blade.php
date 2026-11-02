@extends('admin.layout')
@section('title', $popup->exists ? 'Modifier le popup' : 'Nouveau popup')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('popup.index') }}">Popups</a></li>@endsection

@section('page_header')
<div class="page-bar">
    <div>
        <h1 class="page-bar__title">{{ $popup->exists ? 'Modifier « ' . $popup->titre . ' »' : 'Nouveau popup' }}</h1>
        <p class="page-bar__sub">Ajoutez une affiche (ex. Novembre bleu), puis choisissez quand et où elle s'affiche.</p>
    </div>
</div>
@endsection

@section('content')
@php
    $current = $popup->exists ? $popup->imageUrl() : null;
    $pages   = old('pages', $popup->pages ?? 'tout');
    $freq    = old('frequence', $popup->frequence ?? 'daily');
@endphp

<form action="{{ $popup->exists ? route('popup.update', $popup) : route('popup.store') }}" method="POST" enctype="multipart/form-data" data-once>
    @csrf
    @if($popup->exists) @method('PUT') @endif

    <div class="form-shell">
        <div class="d-flex flex-column gap-3">

            <section class="fsec">
                <h2 class="fsec__title"><i class="bi bi-image"></i>Affiche</h2>
                <div class="dropzone {{ $current ? 'has-image' : '' }} @error('image') border-danger @enderror" data-fit="contain" id="pp-dropzone" style="min-height:260px">
                    @if($current)<img src="{{ $current }}" alt="Affiche actuelle" class="dropzone__preview dropzone__preview--contain">@endif
                    <span class="dropzone__empty d-flex flex-column align-items-center gap-1">
                        <i class="bi bi-cloud-arrow-up"></i>
                        <strong>Glissez l'affiche ici ou cliquez pour choisir @if(!$current)<span class="req">*</span>@endif</strong>
                        <small>JPG, PNG ou WebP · 5 Mo max · format portrait conseillé : 1080 × 1350 px (ou carré 1080 × 1080)</small>
                    </span>
                    <span class="dropzone__change">Changer l'affiche</span>
                    <input type="file" name="image" accept="image/*" aria-label="Affiche du popup">
                </div>
                @error('image')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                <div class="form-text">L'affiche s'affiche en grand, telle quelle. Mettez le message directement sur l'image.</div>

                <div class="mt-3">
                    <label for="pp-titre" class="form-label">Titre <span class="req">*</span></label>
                    <input id="pp-titre" name="titre" value="{{ old('titre', $popup->titre) }}" class="form-control @error('titre') is-invalid @enderror" maxlength="150" required placeholder="Ex. : Novembre bleu 2026">
                    <div class="form-text">Sert à retrouver le popup dans la liste et décrit l'affiche aux personnes malvoyantes. Il n'est pas affiché sur l'image.</div>
                    @error('titre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </section>

            <section class="fsec">
                <h2 class="fsec__title"><i class="bi bi-hand-index"></i>Bouton <span class="text-muted fw-normal small">(facultatif)</span></h2>
                <div class="row g-3">
                    <div class="col-md-5">
                        <label for="pp-btn" class="form-label">Texte du bouton</label>
                        <input id="pp-btn" name="bouton_texte" value="{{ old('bouton_texte', $popup->bouton_texte) }}" class="form-control @error('bouton_texte') is-invalid @enderror" maxlength="60" placeholder="En savoir plus">
                        @error('bouton_texte')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-7">
                        <label for="pp-lien" class="form-label">Lien</label>
                        <input id="pp-lien" name="lien" value="{{ old('lien', $popup->lien) }}" class="form-control @error('lien') is-invalid @enderror" maxlength="500" placeholder="/article/novembre-bleu  ou  https://…">
                        <div class="form-text">Un article du site (commence par /) ou un site externe. Les clics sont comptés.</div>
                        @error('lien')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </section>
        </div>

        <aside class="form-side">
            <section class="fsec">
                <h2 class="fsec__title"><i class="bi bi-calendar-range"></i>Diffusion</h2>
                <div class="switch-row">
                    <div class="switch-row__text"><strong>Actif</strong><span>Désactivez pour mettre en pause sans supprimer</span></div>
                    <input class="form-check-input" type="checkbox" role="switch" name="actif" value="1" {{ old('actif', $popup->actif ?? true) ? 'checked' : '' }} aria-label="Actif">
                </div>
                <div class="mb-3 mt-2">
                    <label for="pp-debut" class="form-label">Début</label>
                    <input id="pp-debut" type="date" name="debut_at" value="{{ old('debut_at', optional($popup->debut_at)->format('Y-m-d')) }}" class="form-control">
                </div>
                <div>
                    <label for="pp-fin" class="form-label">Fin</label>
                    <input id="pp-fin" type="date" name="fin_at" value="{{ old('fin_at', optional($popup->fin_at)->format('Y-m-d')) }}" class="form-control @error('fin_at') is-invalid @enderror">
                    <div class="form-text">Ex. du 1er au 30 novembre. Vide = sans fin. Le popup s'arrête tout seul.</div>
                    @error('fin_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </section>

            <section class="fsec">
                <h2 class="fsec__title"><i class="bi bi-arrow-repeat"></i>Fréquence</h2>
                <div class="d-flex flex-column gap-2">
                    @foreach(\App\Models\Popup::FREQUENCES as $key => $f)
                        <label class="choice">
                            <input class="form-check-input" type="radio" name="frequence" value="{{ $key }}" {{ $freq === $key ? 'checked' : '' }}>
                            <span><strong>{{ $f['label'] }}</strong><small>{{ $f['hint'] }}</small></span>
                        </label>
                    @endforeach
                </div>
            </section>

            <section class="fsec">
                <h2 class="fsec__title"><i class="bi bi-window"></i>Où et quand</h2>
                <div class="d-flex flex-column gap-2 mb-3">
                    @foreach(\App\Models\Popup::PAGES as $key => $pg)
                        <label class="choice">
                            <input class="form-check-input" type="radio" name="pages" value="{{ $key }}" {{ $pages === $key ? 'checked' : '' }}>
                            <span><strong>{{ $pg['label'] }}</strong><small>{{ $pg['hint'] }}</small></span>
                        </label>
                    @endforeach
                </div>
                <label for="pp-delai" class="form-label">Apparaît après</label>
                <div class="input-group" style="max-width:180px">
                    <input id="pp-delai" type="number" min="0" max="30" name="delai" value="{{ old('delai', $popup->delai ?? 3) }}" class="form-control">
                    <span class="input-group-text">secondes</span>
                </div>
                <div class="form-text">Laisser quelques secondes au visiteur avant d'ouvrir le popup est moins intrusif.</div>
            </section>

            @if($popup->exists)
                <section class="fsec">
                    <h2 class="fsec__title"><i class="bi bi-graph-up"></i>Performance</h2>
                    <div class="d-flex justify-content-between mb-1"><span class="text-muted">Affichages</span><strong>{{ number_format($popup->vues, 0, ',', ' ') }}</strong></div>
                    <div class="d-flex justify-content-between mb-1"><span class="text-muted">Clics</span><strong>{{ number_format($popup->clics, 0, ',', ' ') }}</strong></div>
                    <div class="d-flex justify-content-between"><span class="text-muted">Taux de clic</span><strong>{{ number_format($popup->ctr, 1, ',', ' ') }}&nbsp;%</strong></div>
                </section>
            @endif
        </aside>
    </div>

    <div class="form-footer">
        <a href="{{ route('popup.index') }}" class="btn btn-outline-secondary">Annuler</a>
        <div class="form-footer__end">
            <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-check-lg me-1"></i>{{ $popup->exists ? 'Enregistrer les modifications' : 'Créer le popup' }}</button>
        </div>
    </div>
</form>

@if($popup->exists)
    <form action="{{ route('popup.reset', $popup) }}" method="POST" class="mt-3" data-confirm="Remettre à zéro les affichages et les clics ?">
        @csrf @method('PATCH')
        <button type="submit" class="btn btn-link text-muted p-0 small">Remettre les statistiques à zéro</button>
    </form>
@endif
@endsection

@push('scripts')
<script>
    // Titre vide : proposé à partir du nom du fichier (« novembre-bleu-2026.png » → « Novembre bleu 2026 »)
    document.querySelector('#pp-dropzone input[type=file]')?.addEventListener('change', function () {
        var titre = document.getElementById('pp-titre');
        if (!this.files.length || titre.value.trim()) return;
        var name = this.files[0].name.replace(/\.[^.]+$/, '').replace(/[\s_\-]+/g, ' ').trim();
        if (name) titre.value = name.charAt(0).toUpperCase() + name.slice(1);
    });
</script>
@endpush
