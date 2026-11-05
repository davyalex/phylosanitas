@extends('admin.layout')
@section('title', $dossier->exists ? 'Modifier le dossier' : 'Nouveau dossier')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('dossier.index') }}">Dossiers</a></li>@endsection

@section('page_header')
<div class="page-bar">
    <div>
        <h1 class="page-bar__title">{{ $dossier->exists ? 'Modifier « ' . $dossier->titre . ' »' : 'Nouveau dossier' }}</h1>
        <p class="page-bar__sub">Ajoutez les articles puis rangez-les dans l'ordre de lecture conseillé : de la découverte aux conseils pratiques.</p>
    </div>
</div>
@endsection

@section('content')
@php
    $oldIds  = old('articles');
    $choisis = $oldIds
        ? collect($oldIds)->map(fn($id) => $articles->firstWhere('id', (int) $id))->filter()
        : $choisis;
@endphp

<form action="{{ $dossier->exists ? route('dossier.update', $dossier) : route('dossier.store') }}" method="POST" data-once>
    @csrf
    @if($dossier->exists) @method('PUT') @endif

    <div class="form-shell">
        <div class="d-flex flex-column gap-3">
            <section class="fsec">
                <label for="ds-titre" class="form-label">Titre du dossier <span class="req">*</span></label>
                <input id="ds-titre" name="titre" value="{{ old('titre', $dossier->titre) }}" class="form-control form-control-lg @error('titre') is-invalid @enderror" maxlength="150" required placeholder="Ex. : Tout comprendre sur l'hypertension">
                @error('titre')<div class="invalid-feedback">{{ $message }}</div>@enderror

                <label for="ds-intro" class="form-label mt-3">Introduction</label>
                <textarea id="ds-intro" name="intro" rows="3" maxlength="1500" class="form-control" placeholder="Ce que le lecteur va apprendre dans ce dossier.">{{ old('intro', $dossier->intro) }}</textarea>
            </section>

            <section class="fsec">
                <h2 class="fsec__title"><i class="bi bi-list-ol"></i>Articles du dossier <span class="req">*</span></h2>
                @error('articles')<div class="alert alert-danger py-2 small">{{ $message }}</div>@enderror

                <ol class="list-unstyled d-flex flex-column gap-2 mb-3" id="ds-list">
                    @foreach($choisis as $a)
                        <li class="ds-item d-flex align-items-center gap-2 p-2 border rounded bg-white" data-id="{{ $a->id }}">
                            <span class="badge text-bg-primary js-ds-num">{{ $loop->iteration }}</span>
                            <span class="flex-grow-1 small fw-semibold">{{ $a->title }} @if($a->published !== 'public')<span class="badge text-bg-warning ms-1">brouillon</span>@endif</span>
                            <input type="hidden" name="articles[]" value="{{ $a->id }}">
                            <button type="button" class="btn btn-sm btn-outline-secondary icon-action js-ds-up" aria-label="Monter"><i class="bi bi-arrow-up"></i></button>
                            <button type="button" class="btn btn-sm btn-outline-secondary icon-action js-ds-down" aria-label="Descendre"><i class="bi bi-arrow-down"></i></button>
                            <button type="button" class="btn btn-sm btn-outline-danger icon-action js-ds-del" aria-label="Retirer"><i class="bi bi-x-lg"></i></button>
                        </li>
                    @endforeach
                </ol>
                <p class="text-muted small js-ds-empty" @if($choisis->count()) hidden @endif>Aucun article pour l'instant.</p>

                <label for="ds-add" class="form-label">Ajouter un article</label>
                <div class="d-flex gap-2">
                    <select id="ds-add" class="form-select">
                        <option value="">Choisir un article…</option>
                        @foreach($articles as $a)
                            <option value="{{ $a->id }}" data-draft="{{ $a->published !== 'public' ? 1 : 0 }}">{{ Str::limit($a->title, 100) }}</option>
                        @endforeach
                    </select>
                    <button type="button" class="btn btn-outline-primary" id="ds-add-btn"><i class="bi bi-plus-lg"></i><span class="d-none d-md-inline ms-1">Ajouter</span></button>
                </div>
                <div class="form-text">Les articles en brouillon ne s'affichent pas sur le site tant qu'ils ne sont pas publiés.</div>
            </section>
        </div>

        <aside class="form-side">
            <section class="fsec">
                <h2 class="fsec__title"><i class="bi bi-send"></i>Publication</h2>
                <div class="switch-row">
                    <div class="switch-row__text"><strong>En ligne</strong><span>Visible dans la rubrique Dossiers</span></div>
                    <input class="form-check-input" type="checkbox" role="switch" name="actif" value="1" {{ old('actif', $dossier->actif) ? 'checked' : '' }} aria-label="En ligne">
                </div>
                <label for="ds-ordre" class="form-label mt-2">Position</label>
                <input id="ds-ordre" type="number" min="0" max="999" name="ordre" value="{{ old('ordre', $dossier->ordre ?? 0) }}" class="form-control" style="max-width:120px">
                <div class="form-text">0 = en premier. Les dossiers de même position sont classés du plus récent au plus ancien.</div>
            </section>
        </aside>
    </div>

    <div class="form-footer">
        <a href="{{ route('dossier.index') }}" class="btn btn-outline-secondary">Annuler</a>
        <div class="form-footer__end">
            @if($dossier->exists && $dossier->actif)
                <a href="{{ route('dossiers.show', $dossier->slug) }}" target="_blank" rel="noopener" class="btn btn-link"><i class="bi bi-box-arrow-up-right me-1"></i>Voir sur le site</a>
            @endif
            <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-check-lg me-1"></i>{{ $dossier->exists ? 'Enregistrer' : 'Créer le dossier' }}</button>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    (function () {
        var list = document.getElementById('ds-list'), select = document.getElementById('ds-add');
        var empty = document.querySelector('.js-ds-empty');

        function refresh() {
            var items = list.querySelectorAll('.ds-item');
            items.forEach(function (li, i) {
                li.querySelector('.js-ds-num').textContent = i + 1;
                li.querySelector('.js-ds-up').disabled = i === 0;
                li.querySelector('.js-ds-down').disabled = i === items.length - 1;
            });
            empty.hidden = items.length > 0;
            Array.prototype.forEach.call(select.options, function (o) {
                if (o.value) o.disabled = !!list.querySelector('[data-id="' + o.value + '"]');
            });
        }

        document.getElementById('ds-add-btn').addEventListener('click', function () {
            var opt = select.options[select.selectedIndex];
            if (!opt || !opt.value || opt.disabled) return;
            var li = document.createElement('li');
            li.className = 'ds-item d-flex align-items-center gap-2 p-2 border rounded bg-white';
            li.dataset.id = opt.value;
            li.innerHTML = '<span class="badge text-bg-primary js-ds-num"></span>'
                + '<span class="flex-grow-1 small fw-semibold"></span>'
                + '<input type="hidden" name="articles[]" value="' + opt.value + '">'
                + '<button type="button" class="btn btn-sm btn-outline-secondary icon-action js-ds-up" aria-label="Monter"><i class="bi bi-arrow-up"></i></button>'
                + '<button type="button" class="btn btn-sm btn-outline-secondary icon-action js-ds-down" aria-label="Descendre"><i class="bi bi-arrow-down"></i></button>'
                + '<button type="button" class="btn btn-sm btn-outline-danger icon-action js-ds-del" aria-label="Retirer"><i class="bi bi-x-lg"></i></button>';
            li.querySelector('.flex-grow-1').textContent = opt.textContent;
            if (opt.dataset.draft === '1') li.querySelector('.flex-grow-1').insertAdjacentHTML('beforeend', ' <span class="badge text-bg-warning ms-1">brouillon</span>');
            list.appendChild(li);
            select.value = '';
            refresh();
        });

        list.addEventListener('click', function (e) {
            var li = e.target.closest('.ds-item');
            if (!li) return;
            if (e.target.closest('.js-ds-del')) li.remove();
            else if (e.target.closest('.js-ds-up') && li.previousElementSibling) list.insertBefore(li, li.previousElementSibling);
            else if (e.target.closest('.js-ds-down') && li.nextElementSibling) list.insertBefore(li.nextElementSibling, li);
            else return;
            refresh();
        });

        refresh();
    })();
</script>
@endpush
