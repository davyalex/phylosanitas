@extends('site.layout')
@section('title', 'Quiz santé : vrai ou faux ?')
@section('description', 'Testez vos connaissances en santé avec nos quiz « vrai ou faux » : idées reçues, prévention, maladies courantes. Chaque réponse est expliquée.')

@section('content')
<x-svc-hero icon="patch-question" titre="Quiz santé : vrai ou faux ?">
    Démêlez le vrai du faux sur les idées reçues en santé. Chaque réponse est expliquée simplement.
</x-svc-hero>

<section class="svc-body">
    <div class="container-xl">
        @if($quizzes->isEmpty())
            <div class="empty">
                <i class="bi bi-patch-question" aria-hidden="true"></i>
                <h2>Les premiers quiz arrivent bientôt</h2>
                <p>En attendant, découvrez nos articles et nos outils santé.</p>
                <a href="{{ route('outils.index') }}" class="btn btn-medical">Voir les outils santé</a>
            </div>
        @else
            <div class="quiz-list">
                @foreach($quizzes as $q)
                    <article class="qcard">
                        <span class="eyebrow"><i class="bi bi-patch-question" aria-hidden="true"></i>{{ $q->questions_count }} question{{ $q->questions_count > 1 ? 's' : '' }}</span>
                        <h2 class="qcard__title"><a href="{{ route('quiz.jouer', $q->slug) }}">{{ $q->titre }}</a></h2>
                        @if($q->intro)<p>{{ $q->intro }}</p>@endif
                        <div class="meta">
                            <span><i class="bi bi-clock" aria-hidden="true"></i>{{ max(1, (int) ceil($q->questions_count / 3)) }} min</span>
                            @if($q->participations > 0)
                                <span><i class="bi bi-people" aria-hidden="true"></i>{{ number_format($q->participations, 0, ',', ' ') }} participant{{ $q->participations > 1 ? 's' : '' }}</span>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        @endif

        <div class="mini-head"><h2>À découvrir aussi</h2></div>
        @include('site.pages.partials.services-grid', ['except' => 'quiz.liste', 'max' => 3])
    </div>
</section>
@endsection
