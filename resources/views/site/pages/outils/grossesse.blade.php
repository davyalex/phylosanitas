@extends('site.pages.outils._base')

@section('tool_form')
    <div class="field">
        <label for="gr-ddr">Premier jour de vos dernières règles</label>
        <input id="gr-ddr" type="date" required max="{{ now()->toDateString() }}" min="{{ now()->subDays(300)->toDateString() }}">
    </div>
    <div class="field">
        <label for="gr-cycle">Durée habituelle de votre cycle</label>
        <div class="field__unit"><input id="gr-cycle" type="number" inputmode="numeric" min="21" max="40" value="28"><span>jours</span></div>
        <p class="field__hint">28 jours si vous ne savez pas.</p>
    </div>
    <p class="field__error js-err" hidden></p>
@endsection

@section('tool_result')
    <h2>Date prévue d'accouchement</h2>
    <div class="big-figure js-dpa" style="font-size: clamp(1.5rem, 1.2rem + 1.4vw, 2.1rem)"></div>
    <span class="verdict-tag verdict-tag--ok js-trim"></span>
    <ul class="facts">
        <li><span>Vous êtes à</span><strong class="js-sa"></strong></li>
        <li><span>Soit en semaines de grossesse</span><strong class="js-sg"></strong></li>
        <li><span>Début de grossesse estimé</span><strong class="js-conc"></strong></li>
        <li><span>Jours restants avant le terme</span><strong class="js-reste"></strong></li>
    </ul>
    <ol class="timeline js-steps"></ol>
@endsection

@section('tool_explain')
    <h2>Comment est calculée la date ?</h2>
    <div class="prose">
        <p>Le terme est fixé à 40 semaines d'aménorrhée (SA), c'est-à-dire 280 jours après le premier jour des dernières règles pour un cycle de 28 jours. Les médecins comptent en semaines d'aménorrhée (depuis les règles) ; les semaines de grossesse comptent depuis la conception, environ deux semaines plus tard.</p>
        <p>Seuls 5 % des bébés naissent le jour prévu : une naissance entre 37 et 42 SA est normale. L'échographie du premier trimestre permet de préciser la date.</p>
        <p><strong>Suivez vos consultations prénatales (CPN)</strong> : l'Organisation mondiale de la santé recommande au moins 8 contacts pendant la grossesse. Consultez sans attendre en cas de saignements, de fièvre, de maux de tête violents, de gonflement brutal du visage ou des mains, ou si le bébé bouge moins.</p>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.querySelector('.js-tool'), err = form.querySelector('.js-err');
    var DAY = 86400000;
    var addDays = function (d, n) { var x = new Date(d); x.setDate(x.getDate() + n); return x; };

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        var v = document.getElementById('gr-ddr').value;
        var cycle = parseInt(document.getElementById('gr-cycle').value, 10) || 28;
        var today = new Date(); today.setHours(0, 0, 0, 0);
        if (!v) { err.textContent = 'Indiquez la date de vos dernières règles.'; err.hidden = false; return; }
        var ddr = new Date(v + 'T00:00:00');
        var jours = Math.round((today - ddr) / DAY);
        if (jours < 0 || jours > 300) { err.textContent = 'La date doit être passée et dater de moins de 43 semaines.'; err.hidden = false; return; }
        if (cycle < 21 || cycle > 40) cycle = 28;
        err.hidden = true;

        var decal = cycle - 28;
        var dpa = addDays(ddr, 280 + decal), conc = addDays(ddr, 14 + decal);
        var sa = jours - decal, saW = Math.floor(sa / 7), saD = sa % 7;
        var sg = Math.max(0, sa - 14);

        document.querySelector('.js-dpa').textContent = psDate(dpa);
        document.querySelector('.js-sa').textContent = sa < 0 ? '—' : saW + ' SA' + (saD ? ' + ' + saD + ' j' : '');
        document.querySelector('.js-sg').textContent = Math.floor(sg / 7) + ' semaine' + (Math.floor(sg / 7) > 1 ? 's' : '') + (sg % 7 ? ' + ' + (sg % 7) + ' j' : '');
        document.querySelector('.js-conc').textContent = conc.toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' });
        var reste = Math.round((dpa - today) / DAY);
        document.querySelector('.js-reste').textContent = reste > 0 ? psFmt(reste) + ' jours' : 'Terme atteint';
        document.querySelector('.js-trim').textContent = saW < 15 ? '1er trimestre' : saW < 29 ? '2e trimestre' : '3e trimestre';

        var etapes = [
            { sa: 12, t: 'Échographie du 1er trimestre', d: 'Vers 11 à 13 SA : elle précise la date du terme.' },
            { sa: 22, t: 'Échographie du 2e trimestre', d: 'Vers 20 à 24 SA : contrôle de la croissance et de la morphologie.' },
            { sa: 32, t: 'Échographie du 3e trimestre', d: 'Vers 30 à 34 SA : position du bébé et du placenta.' },
            { sa: 37, t: 'Bébé à terme', d: 'À partir de 37 SA, une naissance n\'est plus prématurée.' },
            { sa: 40, t: 'Date prévue d\'accouchement', d: 'Si le bébé n\'est pas né, une surveillance rapprochée est organisée, surtout après 41 SA.' },
        ];
        var ol = document.querySelector('.js-steps'), nextDone = false;
        ol.innerHTML = '';
        etapes.forEach(function (et) {
            var date = addDays(ddr, et.sa * 7 + decal), passe = sa >= et.sa;
            var li = document.createElement('li');
            if (passe) li.className = 'is-done';
            else if (!nextDone) { li.className = 'is-next'; nextDone = true; }
            li.innerHTML = '<span class="timeline__dot"><i class="bi bi-' + (passe ? 'check-lg' : 'calendar-event') + '" aria-hidden="true"></i></span><div><div class="timeline__head"><span></span><span class="timeline__date"></span></div><ul><li></li></ul></div>';
            li.querySelector('.timeline__head span').textContent = et.t;
            li.querySelector('.timeline__date').textContent = et.sa + ' SA · ' + date.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' });
            li.querySelector('ul li').textContent = et.d;
            ol.appendChild(li);
        });
        psShowResult();
    });
});
</script>
@endpush
