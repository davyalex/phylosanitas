@extends('admin.layout')
@section('title', 'Quiz vrai / faux')

@section('page_header')
<div class="page-bar">
    <div>
        <h1 class="page-bar__title">Quiz « Vrai ou faux ? »</h1>
        <p class="page-bar__sub">Des affirmations à juger vraies ou fausses, chacune suivie d'une explication. Très partagés sur WhatsApp, les quiz font découvrir vos articles.</p>
    </div>
    <div class="page-bar__actions">
        <a href="{{ route('quiz.liste') }}" target="_blank" rel="noopener" class="btn btn-outline-secondary"><i class="bi bi-box-arrow-up-right me-1"></i>Voir sur le site</a>
        <a href="{{ route('quiz.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Nouveau quiz</a>
    </div>
</div>
@endsection

@section('content')
<div class="panel-a">
    @if($quizzes->isEmpty())
        <div class="empty-admin">
            <i class="bi bi-patch-question"></i>
            <h3>Aucun quiz</h3>
            <p>Créez un premier quiz de 5 à 10 affirmations sur les idées reçues en santé.</p>
            <a href="{{ route('quiz.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Nouveau quiz</a>
        </div>
    @else
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">Quiz</th>
                        <th>Statut</th>
                        <th class="text-end table-admin-hide">Participants</th>
                        <th class="text-end table-admin-hide">Score moyen</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quizzes as $q)
                        <tr>
                            <td class="ps-3">
                                <a href="{{ route('quiz.edit', $q) }}" class="fw-semibold text-decoration-none">{{ $q->titre }}</a>
                                <div class="small text-muted">{{ $q->questions_count }} affirmation{{ $q->questions_count > 1 ? 's' : '' }} · modifié {{ $q->updated_at->diffForHumans() }}</div>
                            </td>
                            <td>
                                @if($q->actif)
                                    <span class="status status--live">En ligne</span>
                                @else
                                    <span class="status status--off">Brouillon</span>
                                @endif
                            </td>
                            <td class="text-end table-admin-hide">{{ number_format($q->participations, 0, ',', ' ') }}</td>
                            <td class="text-end table-admin-hide">{{ $q->moyenne !== null ? $q->moyenne . ' %' : '—' }}</td>
                            <td class="pe-3">
                                <div class="row-actions">
                                    <form action="{{ route('quiz.toggle', $q) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm {{ $q->actif ? 'btn-outline-warning' : 'btn-outline-success' }} icon-action" title="{{ $q->actif ? 'Retirer du site' : 'Mettre en ligne' }}" aria-label="{{ $q->actif ? 'Retirer du site' : 'Mettre en ligne' }}"><i class="bi bi-{{ $q->actif ? 'pause-fill' : 'play-fill' }}"></i></button>
                                    </form>
                                    @if($q->actif)
                                        <a href="{{ route('quiz.jouer', $q->slug) }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary icon-action" title="Voir sur le site" aria-label="Voir sur le site"><i class="bi bi-eye"></i></a>
                                    @endif
                                    <a href="{{ route('quiz.edit', $q) }}" class="btn btn-sm btn-outline-primary icon-action" title="Modifier" aria-label="Modifier"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('quiz.destroy', $q) }}" method="POST" data-confirm="Supprimer le quiz « {{ $q->titre }} » ?">
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
