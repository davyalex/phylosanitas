@php
    $data = $quiz->questions->map(fn($q) => [
        'a'    => $q->affirmation,
        'r'    => (bool) $q->reponse,
        'e'    => $q->explication,
        'post' => ($q->post && $q->post->published === 'public')
            ? ['titre' => $q->post->title, 'url' => route('post.detail', ['slug' => $q->post->slug])]
            : null,
    ])->values();
    $shareUrl = route('quiz.jouer', $quiz->slug);
@endphp

@extends('site.layout')
@section('title', $quiz->titre . ' — Quiz vrai ou faux')
@section('description', $quiz->intro ?: 'Quiz santé vrai ou faux : ' . $data->count() . ' affirmations à démêler, chaque réponse est expliquée.')
@section('canonical', $shareUrl)

@push('jsonld')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type'    => 'Quiz',
    'name'     => $quiz->titre,
    'about'    => 'Santé',
    'inLanguage' => 'fr-FR',
    'educationalLevel' => 'grand public',
    'hasPart'  => $data->map(fn($q) => [
        '@type' => 'Question',
        'name'  => $q['a'],
        'acceptedAnswer' => ['@type' => 'Answer', 'text' => ($q['r'] ? 'Vrai. ' : 'Faux. ') . $q['e']],
    ])->all(),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@section('content')
<x-svc-hero icon="patch-question" :titre="$quiz->titre" :parent="['Quiz santé' => route('quiz.liste')]">
    {{ $quiz->intro ?: 'Vrai ou faux ? Répondez, puis découvrez l\'explication.' }}
</x-svc-hero>

<section class="svc-body">
    <div class="container-xl">
        <div class="quiz" id="quiz" data-score-url="{{ route('quiz.score', $quiz) }}">
            <div class="quiz__card">
                <div class="quiz__progress" aria-hidden="true"><div class="quiz__bar js-bar"></div></div>

                {{-- Question en cours --}}
                <div class="quiz__body js-play">
                    <div class="quiz__step">
                        <span>Question <span class="js-num">1</span> sur {{ $data->count() }}</span>
                        <span><i class="bi bi-check2-circle" aria-hidden="true"></i> <span class="js-good">0</span> bonne<span class="js-good-s"></span> réponse<span class="js-good-s"></span></span>
                    </div>
                    <p class="quiz__q js-q" aria-live="polite">{{ $data[0]['a'] ?? '' }}</p>
                    <div class="quiz__choices">
                        <button type="button" class="btn btn-true quiz__choice js-pick" data-pick="1"><i class="bi bi-check-circle" aria-hidden="true"></i>Vrai</button>
                        <button type="button" class="btn btn-false quiz__choice js-pick" data-pick="0"><i class="bi bi-x-circle" aria-hidden="true"></i>Faux</button>
                    </div>
                    <div class="quiz__verdict js-verdict" tabindex="-1" hidden>
                        <p class="quiz__verdict-title js-verdict-title"></p>
                        <p class="js-verdict-text"></p>
                        <a href="#" class="quiz__more js-verdict-more" hidden><i class="bi bi-book" aria-hidden="true"></i><span></span></a>
                    </div>
                    <div class="quiz__next js-next-wrap" hidden>
                        <button type="button" class="btn btn-medical js-next">Question suivante <i class="bi bi-arrow-right" aria-hidden="true"></i></button>
                    </div>
                </div>

                {{-- Résultat --}}
                <div class="quiz__body quiz__result js-result" hidden tabindex="-1">
                    <div class="quiz__score js-score-ring"><span class="js-score">0/0</span></div>
                    <h2 class="js-result-title"></h2>
                    <p class="js-result-text"></p>
                    <div class="quiz__actions">
                        <a href="#" class="btn btn-whatsapp js-share" target="_blank" rel="noopener"><i class="bi bi-whatsapp" aria-hidden="true"></i>Défier mes proches</a>
                        <button type="button" class="btn btn-outline-medical js-replay"><i class="bi bi-arrow-repeat" aria-hidden="true"></i>Rejouer</button>
                    </div>
                    <div class="quiz__recap">
                        <h3>Le récapitulatif</h3>
                        <ol class="js-recap"></ol>
                    </div>
                </div>
            </div>

            <aside class="disclaimer" aria-label="Avertissement">
                <i class="bi bi-info-circle" aria-hidden="true"></i>
                <p>Ce quiz informe et ne remplace pas l'avis d'un professionnel de santé.@if(config('sante.modules.questions')) Une question sur votre situation ? <a href="{{ route('questions.index') }}">Le médecin vous répond</a>.@endif</p>
            </aside>
        </div>

        @if($autres->count())
            <div class="mini-head"><h2>Autres quiz</h2><a href="{{ route('quiz.liste') }}" class="sec-head__link">Tous les quiz <i class="bi bi-arrow-right" aria-hidden="true"></i></a></div>
            <div class="quiz-list">
                @foreach($autres as $q)
                    <article class="qcard">
                        <span class="eyebrow"><i class="bi bi-patch-question" aria-hidden="true"></i>{{ $q->questions_count }} questions</span>
                        <h3 class="qcard__title"><a href="{{ route('quiz.jouer', $q->slug) }}">{{ $q->titre }}</a></h3>
                        @if($q->intro)<p>{{ $q->intro }}</p>@endif
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var Q = @json($data), root = document.getElementById('quiz');
    if (!root || !Q.length) return;
    var $ = function (s) { return root.querySelector(s); };
    var i = 0, good = 0, answers = [], sent = false;
    var share = @json($shareUrl), titre = @json($quiz->titre);

    function show() {
        var q = Q[i];
        $('.js-num').textContent = i + 1;
        $('.js-q').textContent = q.a;
        $('.js-bar').style.width = (i / Q.length * 100) + '%';
        root.querySelectorAll('.js-pick').forEach(function (b) { b.disabled = false; b.classList.remove('is-picked'); });
        $('.js-verdict').hidden = true;
        $('.js-next-wrap').hidden = true;
    }

    function pick(btn) {
        var q = Q[i], choice = btn.getAttribute('data-pick') === '1', ok = choice === q.r;
        if (ok) good++;
        answers.push(ok);
        root.querySelectorAll('.js-pick').forEach(function (b) { b.disabled = true; });
        btn.classList.add('is-picked');

        var v = $('.js-verdict');
        v.className = 'quiz__verdict js-verdict ' + (ok ? 'is-ok' : 'is-ko');
        $('.js-verdict-title').innerHTML = '<i class="bi bi-' + (ok ? 'check-circle-fill' : 'x-circle-fill') + '" aria-hidden="true"></i>';
        $('.js-verdict-title').appendChild(document.createTextNode((ok ? 'Bonne réponse ! ' : 'Raté ! ') + 'C\'est ' + (q.r ? 'vrai.' : 'faux.')));
        $('.js-verdict-text').textContent = q.e;
        var more = $('.js-verdict-more');
        more.hidden = !q.post;
        if (q.post) { more.href = q.post.url; more.querySelector('span').textContent = 'Pour en savoir plus : ' + q.post.titre; }
        v.hidden = false;

        $('.js-good').textContent = good;
        root.querySelectorAll('.js-good-s').forEach(function (s) { s.textContent = good > 1 ? 's' : ''; });
        $('.js-next').innerHTML = i === Q.length - 1 ? 'Voir mon résultat <i class="bi bi-flag" aria-hidden="true"></i>' : 'Question suivante <i class="bi bi-arrow-right" aria-hidden="true"></i>';
        $('.js-next-wrap').hidden = false;
        $('.js-bar').style.width = ((i + 1) / Q.length * 100) + '%';
        v.focus({ preventScroll: true });
        v.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function result() {
        var pct = Math.round(good / Q.length * 100);
        $('.js-play').hidden = true;
        var r = $('.js-result');
        r.hidden = false;
        $('.js-score').textContent = good + '/' + Q.length;
        $('.js-score-ring').style.setProperty('--p', pct);
        $('.js-result-title').textContent = pct === 100 ? 'Sans faute, bravo !' : pct >= 70 ? 'Très bien joué !' : pct >= 40 ? 'Pas mal, vous progressez !' : 'Vous avez appris des choses !';
        $('.js-result-text').textContent = pct >= 70
            ? 'Vous connaissez bien le sujet. Partagez ce quiz pour aider vos proches à démêler le vrai du faux.'
            : 'Les idées reçues ont la vie dure. Relisez les explications ci-dessous et rejouez quand vous voulez.';
        $('.js-share').href = 'https://wa.me/?text=' + encodeURIComponent('J\'ai eu ' + good + '/' + Q.length + ' au quiz « ' + titre + ' ». Et toi ? ' + share);

        var ol = $('.js-recap');
        ol.innerHTML = '';
        Q.forEach(function (q, n) {
            var li = document.createElement('li');
            li.innerHTML = '<i class="bi ' + (answers[n] ? 'bi-check-circle-fill' : 'bi-x-circle-fill') + '" aria-hidden="true"></i><span></span>';
            li.querySelector('span').textContent = q.a + ' — ' + (q.r ? 'Vrai' : 'Faux');
            ol.appendChild(li);
        });
        r.focus({ preventScroll: true });
        root.scrollIntoView({ behavior: 'smooth', block: 'start' });

        if (!sent) {
            sent = true;
            var token = (document.querySelector('meta[name="csrf-token"]') || {}).content;
            fetch(root.getAttribute('data-score-url'), {
                method: 'POST', credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
                body: JSON.stringify({ score: good }),
            }).then(function (res) { return res.json(); }).then(function (d) {
                if (d && d.moyenne !== null && d.moyenne !== undefined) {
                    $('.js-result-text').textContent += ' Score moyen des participants : ' + d.moyenne + ' %.';
                }
            }).catch(function () {});
        }
    }

    root.querySelectorAll('.js-pick').forEach(function (b) { b.addEventListener('click', function () { pick(b); }); });
    $('.js-next').addEventListener('click', function () {
        if (i === Q.length - 1) return result();
        i++; show();
        $('.js-q').focus && $('.js-q').scrollIntoView({ behavior: 'smooth', block: 'center' });
    });
    $('.js-replay').addEventListener('click', function () {
        i = 0; good = 0; answers = [];
        $('.js-good').textContent = '0';
        $('.js-result').hidden = true; $('.js-play').hidden = false;
        show();
    });
    show();
});
</script>
@endpush
@endsection
