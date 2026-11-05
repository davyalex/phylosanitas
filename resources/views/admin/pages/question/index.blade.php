@extends('admin.layout')
@section('title', 'Le médecin répond')

@section('page_header')
<div class="page-bar">
    <div>
        <h1 class="page-bar__title">Le médecin répond</h1>
        <p class="page-bar__sub">Les questions envoyées par les lecteurs. Répondez, puis publiez : la question et la réponse apparaissent sur le site sans le nom ni l'email du lecteur.</p>
    </div>
    <div class="page-bar__actions">
        <a href="{{ route('questions.index') }}" target="_blank" rel="noopener" class="btn btn-outline-secondary"><i class="bi bi-box-arrow-up-right me-1"></i>Voir sur le site</a>
    </div>
</div>
@endsection

@section('content')
@php
    $onglets = ['nouvelle' => ['À traiter', 'inbox'], 'publiee' => ['Publiées', 'check2-circle'], 'rejetee' => ['Sans suite', 'archive']];
@endphp

<nav class="tabs-pill" aria-label="Filtrer les questions">
    @foreach($onglets as $key => [$label, $icon])
        <a href="{{ route('question.index', ['statut' => $key]) }}" class="{{ $statut === $key ? 'is-active' : '' }}" @if($statut === $key) aria-current="page" @endif>
            <i class="bi bi-{{ $icon }} me-1"></i>{{ $label }}<span>{{ $compteurs[$key] ?? 0 }}</span>
        </a>
    @endforeach
</nav>

<div class="panel-a">
    @if($questions->isEmpty())
        <div class="empty-admin">
            <i class="bi bi-chat-heart"></i>
            <h3>{{ $statut === 'nouvelle' ? 'Aucune question en attente' : 'Rien ici pour le moment' }}</h3>
            <p>Les lecteurs posent leurs questions depuis la page « Le médecin répond » et depuis la fin de chaque article.</p>
        </div>
    @else
        <div class="d-flex flex-column">
            @foreach($questions as $q)
                <a href="{{ route('question.edit', $q) }}" class="inbox-row {{ $statut === 'nouvelle' ? 'is-new' : '' }}">
                    <span class="inbox-row__avatar" aria-hidden="true">{{ strtoupper(mb_substr($q->nom, 0, 1)) }}</span>
                    <span class="d-block" style="min-width:0">
                        <span class="inbox-row__from"><strong>{{ $q->nom }}</strong>@if($q->tranche_age) · {{ $q->tranche_age }}@endif @if($q->reponse && $statut === 'nouvelle')<span class="status status--scheduled ms-1">Brouillon de réponse</span>@endif</span>
                        <span class="inbox-row__subject d-block">{{ $q->titre ?: Str::limit($q->question, 90) }}</span>
                        <span class="inbox-row__excerpt d-block">{{ Str::limit($q->question, 140) }}</span>
                    </span>
                    <span class="inbox-row__date">
                        @if($statut === 'publiee')
                            {{ $q->repondu_at?->diffForHumans() }} · {{ $q->vues }} lecture{{ $q->vues > 1 ? 's' : '' }}
                        @else
                            {{ $q->created_at->diffForHumans() }}
                        @endif
                    </span>
                </a>
            @endforeach
        </div>
        @if($questions->hasPages())<div class="p-3">{{ $questions->links('pagination::bootstrap-5') }}</div>@endif
    @endif
</div>
@endsection
