@extends('admin.layout')
@section('title', 'Répondre à une question')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('question.index') }}">Le médecin répond</a></li>@endsection

@section('page_header')
<div class="page-bar">
    <div>
        <h1 class="page-bar__title">Question de {{ $question->nom }}</h1>
        <p class="page-bar__sub">Reçue {{ $question->created_at->diffForHumans() }} ({{ $question->created_at->translatedFormat('d F Y à H:i') }})
            @if($question->tranche_age) · {{ $question->tranche_age }}@endif
            @if($question->email) · <a href="mailto:{{ $question->email }}">{{ $question->email }}</a>@endif</p>
    </div>
    @if($question->statut === 'publiee' && $question->slug)
        <div class="page-bar__actions">
            <a href="{{ route('questions.show', $question->slug) }}" target="_blank" rel="noopener" class="btn btn-outline-secondary"><i class="bi bi-box-arrow-up-right me-1"></i>Voir sur le site</a>
        </div>
    @endif
</div>
@endsection

@section('content')
<form action="{{ route('question.update', $question) }}" method="POST" data-once>
    @csrf @method('PUT')

    <div class="form-shell">
        <div class="d-flex flex-column gap-3">
            <section class="fsec">
                <h2 class="fsec__title"><i class="bi bi-question-circle"></i>Question du lecteur</h2>
                <label for="qs-question" class="form-label">Texte publié</label>
                <textarea id="qs-question" name="question" rows="5" maxlength="2000" class="form-control @error('question') is-invalid @enderror">{{ old('question', $question->question) }}</textarea>
                <div class="form-text">Vous pouvez corriger l'orthographe ou retirer un détail qui permettrait de reconnaître la personne (nom, lieu de travail…).</div>

                <label for="qs-titre" class="form-label mt-3">Titre <span class="text-muted fw-normal">(obligatoire pour publier)</span></label>
                <input id="qs-titre" name="titre" value="{{ old('titre', $question->titre) }}" maxlength="200" class="form-control @error('titre') is-invalid @enderror"
                       placeholder="Ex. : Peut-on faire du sport quand on a de l'hypertension ?">
                @error('titre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">Une question courte et claire : c'est elle que les gens tapent dans Google.</div>
            </section>

            <section class="fsec">
                <h2 class="fsec__title"><i class="bi bi-chat-square-text"></i>Réponse</h2>
                <textarea id="qs-reponse" name="reponse" rows="12" maxlength="10000" class="form-control @error('reponse') is-invalid @enderror"
                          placeholder="Répondez simplement. Laissez une ligne vide entre deux paragraphes.&#10;&#10;Terminez si besoin par : quand consulter.">{{ old('reponse', $question->reponse) }}</textarea>
                @error('reponse')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">Le rappel « Cette réponse ne remplace pas une consultation » est ajouté automatiquement sur le site.</div>
            </section>
        </div>

        <aside class="form-side">
            <section class="fsec">
                <h2 class="fsec__title"><i class="bi bi-flag"></i>Statut</h2>
                <p class="mb-2">
                    @switch($question->statut)
                        @case('publiee') <span class="status status--live">Publiée</span> @break
                        @case('rejetee') <span class="status status--off">Sans suite</span> @break
                        @default <span class="status status--new">À traiter</span>
                    @endswitch
                </p>

                <label for="qs-cat" class="form-label">Thème</label>
                <select id="qs-cat" name="category_id" class="form-select mb-3">
                    <option value="">— Aucun —</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" {{ (string) old('category_id', $question->category_id) === (string) $c->id ? 'selected' : '' }}>{{ $c->title }}</option>
                    @endforeach
                </select>

                <label for="qs-med" class="form-label">Répondu par</label>
                <select id="qs-med" name="repondu_par" class="form-select">
                    @foreach($medecins as $u)
                        <option value="{{ $u->id }}" {{ (string) old('repondu_par', $question->repondu_par ?? auth()->id()) === (string) $u->id ? 'selected' : '' }}>{{ $u->est_medecin ? '🩺 ' : '' }}{{ $u->name }}{{ $u->titre_pro ? ' — ' . $u->titre_pro : '' }}</option>
                    @endforeach
                </select>
                <div class="form-text">Le nom s'affiche si le profil public du compte est activé.</div>
            </section>
        </aside>
    </div>

    <div class="form-footer">
        <a href="{{ route('question.index', ['statut' => $question->statut]) }}" class="btn btn-outline-secondary">Retour</a>
        <div class="form-footer__end">
            <button type="submit" name="action" value="brouillon" class="btn btn-outline-primary">Enregistrer le brouillon</button>
            <button type="submit" name="action" value="publier" class="btn btn-primary btn-lg"><i class="bi bi-send-check me-1"></i>{{ $question->statut === 'publiee' ? 'Mettre à jour la publication' : 'Publier la réponse' }}</button>
        </div>
    </div>
</form>

<div class="d-flex gap-3 mt-3 flex-wrap">
    @if($question->statut === 'publiee')
        <form action="{{ route('question.depublier', $question) }}" method="POST" data-confirm="Retirer cette question du site ? Elle repassera dans « À traiter ».">
            @csrf @method('PATCH')
            <button type="submit" class="btn btn-link text-muted p-0 small">Retirer du site</button>
        </form>
    @elseif($question->statut === 'nouvelle')
        <form action="{{ route('question.rejeter', $question) }}" method="POST" data-confirm="Classer cette question sans suite ? Elle ne sera pas publiée." data-confirm-ok="Classer sans suite">
            @csrf @method('PATCH')
            <button type="submit" class="btn btn-link text-muted p-0 small">Classer sans suite</button>
        </form>
    @endif
    <form action="{{ route('question.destroy', $question) }}" method="POST" data-confirm="Supprimer définitivement cette question ?">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-link text-danger p-0 small">Supprimer</button>
    </form>
</div>
@endsection
