@extends('admin.layout')
@section('title', $quiz->exists ? 'Modifier le quiz' : 'Nouveau quiz')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('quiz.index') }}">Quiz</a></li>@endsection

@section('page_header')
<div class="page-bar">
    <div>
        <h1 class="page-bar__title">{{ $quiz->exists ? 'Modifier « ' . $quiz->titre . ' »' : 'Nouveau quiz' }}</h1>
        <p class="page-bar__sub">Pour chaque affirmation : la bonne réponse (vrai ou faux) et une explication courte. Idéal : 5 à 10 affirmations.</p>
    </div>
</div>
@endsection

@section('content')
@php
    $questions = old('questions', $quiz->exists
        ? $quiz->questions->map(fn($q) => ['affirmation' => $q->affirmation, 'reponse' => $q->reponse ? '1' : '0', 'explication' => $q->explication, 'post_id' => $q->post_id])->all()
        : [['affirmation' => '', 'reponse' => '', 'explication' => '', 'post_id' => '']]);
@endphp

<form action="{{ $quiz->exists ? route('quiz.update', $quiz) : route('quiz.store') }}" method="POST" data-once id="quiz-form">
    @csrf
    @if($quiz->exists) @method('PUT') @endif

    <div class="form-shell">
        <div class="d-flex flex-column gap-3">
            <section class="fsec">
                <div class="mb-3">
                    <label for="qz-titre" class="form-label">Titre du quiz <span class="req">*</span></label>
                    <input id="qz-titre" name="titre" value="{{ old('titre', $quiz->titre) }}" class="form-control form-control-lg @error('titre') is-invalid @enderror" maxlength="150" required placeholder="Ex. : Paludisme : vrai ou faux ?">
                    @error('titre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <label for="qz-intro" class="form-label">Introduction</label>
                <textarea id="qz-intro" name="intro" rows="2" class="form-control" maxlength="500" placeholder="Une phrase qui donne envie de jouer.">{{ old('intro', $quiz->intro) }}</textarea>
            </section>

            <div id="qz-list" class="d-flex flex-column gap-3">
                @foreach($questions as $i => $q)
                    @include('admin.pages.quiz._question', ['i' => $i, 'q' => $q, 'n' => $loop->iteration])
                @endforeach
            </div>

            <button type="button" class="btn btn-outline-primary align-self-start" id="qz-add"><i class="bi bi-plus-lg me-1"></i>Ajouter une affirmation</button>
        </div>

        <aside class="form-side">
            <section class="fsec">
                <h2 class="fsec__title"><i class="bi bi-send"></i>Publication</h2>
                <div class="switch-row">
                    <div class="switch-row__text"><strong>En ligne</strong><span>Sinon, le quiz reste en brouillon</span></div>
                    <input class="form-check-input" type="checkbox" role="switch" name="actif" value="1" {{ old('actif', $quiz->actif) ? 'checked' : '' }} aria-label="En ligne">
                </div>
                <p class="form-text mb-0">Faites relire les affirmations et les explications par un médecin avant de mettre le quiz en ligne.</p>
            </section>

            @if($quiz->exists)
                <section class="fsec">
                    <h2 class="fsec__title"><i class="bi bi-graph-up"></i>Résultats</h2>
                    <div class="d-flex justify-content-between mb-1"><span class="text-muted">Participants</span><strong>{{ number_format($quiz->participations, 0, ',', ' ') }}</strong></div>
                    <div class="d-flex justify-content-between"><span class="text-muted">Score moyen</span><strong>{{ $quiz->moyenne !== null ? $quiz->moyenne . ' %' : '—' }}</strong></div>
                </section>
            @endif

            <section class="fsec">
                <h2 class="fsec__title"><i class="bi bi-lightbulb"></i>Conseils</h2>
                <ul class="small text-muted ps-3 mb-0">
                    <li>Partez des idées reçues entendues en consultation.</li>
                    <li>Alternez les vraies et les fausses.</li>
                    <li>Une explication de 1 à 3 phrases, sans jargon.</li>
                    <li>Liez un article pour aller plus loin : c'est ce qui fait découvrir le site.</li>
                </ul>
            </section>
        </aside>
    </div>

    <div class="form-footer">
        <a href="{{ route('quiz.index') }}" class="btn btn-outline-secondary">Annuler</a>
        <div class="form-footer__end">
            @if($quiz->exists && $quiz->actif)
                <a href="{{ route('quiz.jouer', $quiz->slug) }}" target="_blank" rel="noopener" class="btn btn-link"><i class="bi bi-box-arrow-up-right me-1"></i>Voir sur le site</a>
            @endif
            <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-check-lg me-1"></i>{{ $quiz->exists ? 'Enregistrer' : 'Créer le quiz' }}</button>
        </div>
    </div>
</form>

@if($quiz->exists)
    <form action="{{ route('quiz.destroy', $quiz) }}" method="POST" class="mt-3" data-confirm="Supprimer le quiz « {{ $quiz->titre }} » ?">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-link text-danger p-0 small">Supprimer ce quiz</button>
    </form>
@endif

<template id="qz-tpl">
    @include('admin.pages.quiz._question', ['i' => '__I__', 'q' => ['affirmation' => '', 'reponse' => '', 'explication' => '', 'post_id' => ''], 'n' => '__N__'])
</template>
@endsection

@push('scripts')
<script>
    (function () {
        var list = document.getElementById('qz-list'), tpl = document.getElementById('qz-tpl');
        var next = list.children.length;

        function renumber() {
            Array.prototype.forEach.call(list.children, function (card, i) {
                var n = card.querySelector('.js-qz-num');
                if (n) n.textContent = i + 1;
                card.querySelector('.js-qz-up').disabled = i === 0;
                card.querySelector('.js-qz-down').disabled = i === list.children.length - 1;
                card.querySelector('.js-qz-del').disabled = list.children.length === 1;
            });
        }

        document.getElementById('qz-add').addEventListener('click', function () {
            var html = tpl.innerHTML.replace(/__I__/g, 'n' + next++).replace(/__N__/g, list.children.length + 1);
            list.insertAdjacentHTML('beforeend', html);
            renumber();
            list.lastElementChild.querySelector('textarea, input').focus();
        });

        list.addEventListener('click', function (e) {
            var card = e.target.closest('.qz-card');
            if (!card) return;
            if (e.target.closest('.js-qz-del')) card.remove();
            else if (e.target.closest('.js-qz-up') && card.previousElementSibling) list.insertBefore(card, card.previousElementSibling);
            else if (e.target.closest('.js-qz-down') && card.nextElementSibling) list.insertBefore(card.nextElementSibling, card);
            else return;
            renumber();
        });

        renumber();
    })();
</script>
@endpush
