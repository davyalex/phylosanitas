{{-- Accueil : services santé + quiz du moment. Variable : $quizAccueil (Quiz|null) --}}
@php
    $qq = ($quizAccueil ?? null) && $quizAccueil->questions->count() ? $quizAccueil->questions->random() : null;
@endphp

<section class="section home-svc" aria-labelledby="svc-title">
    <div class="container-xl">
        <div class="home-svc__head">
            <div>
                <span class="eyebrow"><i class="bi bi-grid" aria-hidden="true"></i>Services santé</span>
                <h2 id="svc-title" class="home-svc__title">Plus qu'un blog : des services pour votre santé</h2>
                <p class="home-svc__lead">Pharmacies de garde, outils de calcul, quiz, calendrier santé… gratuits et accessibles à tous.</p>
            </div>
        </div>

        <div class="{{ $qq ? 'home-svc__layout' : '' }}">
            @include('site.pages.partials.services-grid', [
                'except' => $qq ? ['quiz.liste'] : [],
                'max'    => 6,
                'class'  => $qq ? '' : 'svc-grid--3',
            ])

            @if($qq)
                <aside class="quiz-teaser js-quiz-teaser" data-answer="{{ $qq->reponse ? 1 : 0 }}" aria-labelledby="teaser-title">
                    <span class="quiz-teaser__label" id="teaser-title"><i class="bi bi-patch-question-fill" aria-hidden="true"></i>Vrai ou faux ?</span>
                    <p class="quiz-teaser__q">{{ $qq->affirmation }}</p>
                    <div class="quiz-teaser__btns js-teaser-btns">
                        <button type="button" class="btn btn-true" data-pick="1"><i class="bi bi-check-circle" aria-hidden="true"></i>Vrai</button>
                        <button type="button" class="btn btn-false" data-pick="0"><i class="bi bi-x-circle" aria-hidden="true"></i>Faux</button>
                    </div>
                    <div class="quiz-teaser__answer js-teaser-answer" tabindex="-1" role="status" hidden>
                        <strong></strong>
                        {{ Str::limit($qq->explication, 220) }}
                    </div>
                    <p class="quiz-teaser__foot">
                        <a href="{{ route('quiz.jouer', $quizAccueil->slug) }}">Faire tout le quiz « {{ $quizAccueil->titre }} » <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                    </p>
                </aside>
            @endif
        </div>
    </div>
</section>
