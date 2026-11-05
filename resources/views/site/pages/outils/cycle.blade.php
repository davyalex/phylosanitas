@extends('site.pages.outils._base')

@section('tool_form')
    <div class="field">
        <label for="cy-ddr">Premier jour de vos dernières règles</label>
        <input id="cy-ddr" type="date" required max="{{ now()->toDateString() }}" min="{{ now()->subDays(120)->toDateString() }}">
    </div>
    <div class="field-row">
        <div class="field">
            <label for="cy-cycle">Durée de votre cycle</label>
            <div class="field__unit"><input id="cy-cycle" type="number" inputmode="numeric" min="21" max="45" value="28"><span>jours</span></div>
            <p class="field__hint">Du 1er jour des règles au 1er jour des suivantes. 28 jours si vous ne savez pas.</p>
        </div>
        <div class="field">
            <label for="cy-regles">Durée des règles</label>
            <div class="field__unit"><input id="cy-regles" type="number" inputmode="numeric" min="2" max="10" value="5"><span>jours</span></div>
        </div>
    </div>
    <p class="field__error js-err" hidden></p>
@endsection

@section('tool_result')
    <h2>Vos prochaines règles</h2>
    <div class="big-figure js-next" style="font-size: clamp(1.5rem, 1.2rem + 1.4vw, 2.1rem)"></div>
    <span class="verdict-tag verdict-tag--ok js-phase"></span>

    <ul class="facts">
        <li><span>Ovulation estimée</span><strong class="js-ovu"></strong></li>
        <li><span>Période fertile estimée</span><strong class="js-fertile"></strong></li>
        <li><span>Aujourd'hui</span><strong class="js-jour"></strong></li>
    </ul>

    <div class="cy-cal js-cal" aria-hidden="true"></div>
    <ul class="cy-legend" aria-label="Légende du calendrier">
        <li><span class="cy-dot cy-dot--regles"></span>Règles</li>
        <li><span class="cy-dot cy-dot--fertile"></span>Période fertile</li>
        <li><span class="cy-dot cy-dot--ovu"></span>Ovulation</li>
    </ul>

    <h3 class="cy-subtitle">Les 3 prochains cycles</h3>
    <ol class="timeline js-steps"></ol>

    <div class="tool-note js-note"><i class="bi bi-exclamation-triangle" aria-hidden="true"></i>
        <p>Ce calendrier est une estimation : l'ovulation peut varier d'un mois à l'autre. <strong>Ce n'est pas une méthode de contraception</strong> et il ne garantit pas une grossesse.</p></div>
@endsection

@section('tool_explain')
    <h2>Comprendre son cycle</h2>
    <div class="prose">
        <p>Le cycle menstruel commence le premier jour des règles et se termine la veille des règles suivantes. Il dure en moyenne 28 jours, mais un cycle entre 21 et 35 jours est considéré comme normal chez l'adulte.</p>
        <ul>
            <li><strong>Les règles</strong> durent généralement de 3 à 7 jours.</li>
            <li><strong>L'ovulation</strong> a lieu environ 14 jours avant les règles suivantes : au 14e jour pour un cycle de 28 jours, plus tard si le cycle est plus long.</li>
            <li><strong>La période fertile</strong> couvre les 5 jours qui précèdent l'ovulation et le jour qui la suit : les spermatozoïdes peuvent survivre quelques jours.</li>
        </ul>
        <p>Le stress, une maladie, un voyage, l'allaitement ou certains médicaments peuvent décaler l'ovulation. Plus les cycles sont irréguliers, moins l'estimation est fiable.</p>
        <p><strong>Consultez un médecin ou une sage-femme</strong> si vos cycles durent moins de 21 jours ou plus de 35 jours, si vos règles sont très abondantes ou très douloureuses, en cas de saignements entre les règles, ou si vos règles sont absentes depuis plus de 3 mois. Un retard de règles avec des rapports non protégés doit faire penser à une grossesse : un test peut se faire en pharmacie.</p>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.querySelector('.js-tool'), err = form.querySelector('.js-err');
    var DAY = 86400000;
    var add = function (d, n) { var x = new Date(d); x.setDate(x.getDate() + n); return x; };
    var diff = function (a, b) { return Math.round((b - a) / DAY); };
    var court = function (d) { return d.toLocaleDateString('fr-FR', { weekday: 'short', day: 'numeric', month: 'long' }); };
    var jourMois = function (d) { return d.toLocaleDateString('fr-FR', { day: 'numeric', month: 'long' }); };

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        var v = document.getElementById('cy-ddr').value;
        var C = parseInt(document.getElementById('cy-cycle').value, 10);
        var R = parseInt(document.getElementById('cy-regles').value, 10);
        var today = new Date(); today.setHours(0, 0, 0, 0);

        if (!v) { err.textContent = 'Indiquez la date de vos dernières règles.'; err.hidden = false; return; }
        if (!(C >= 21 && C <= 45)) { err.textContent = 'La durée du cycle doit être comprise entre 21 et 45 jours.'; err.hidden = false; return; }
        if (!(R >= 2 && R <= 10) || R >= C) { err.textContent = 'La durée des règles doit être comprise entre 2 et 10 jours.'; err.hidden = false; return; }
        var ddr = new Date(v + 'T00:00:00');
        var ecart = diff(ddr, today);
        if (ecart < 0 || ecart > 120) { err.textContent = 'La date doit être passée et dater de moins de 4 mois.'; err.hidden = false; return; }
        err.hidden = true;

        // Début du cycle en cours (si plusieurs cycles se sont écoulés depuis la date saisie)
        var debut = add(ddr, Math.floor(ecart / C) * C);
        var prochaine = add(debut, C);
        var ovu = add(prochaine, -14);
        // Ovulation déjà passée dans ce cycle : on montre celle du cycle suivant
        var ovuAff = ovu < today && add(ovu, 1) < today ? add(ovu, C) : ovu;
        var jourCycle = diff(debut, today) + 1;

        // Phase du jour
        var phase;
        if (jourCycle <= R) phase = 'Règles';
        else if (diff(today, ovu) === 0) phase = 'Ovulation estimée';
        else if (today >= add(ovu, -5) && today <= add(ovu, 1)) phase = 'Période fertile';
        else if (today < ovu) phase = 'Avant l\'ovulation';
        else phase = 'Après l\'ovulation';

        document.querySelector('.js-next').textContent = prochaine.toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long' });
        var tag = document.querySelector('.js-phase');
        tag.textContent = 'Jour ' + jourCycle + ' du cycle · ' + phase;
        tag.className = 'verdict-tag js-phase ' + (phase === 'Règles' ? 'verdict-tag--danger' : 'verdict-tag--ok');
        document.querySelector('.js-ovu').textContent = court(ovuAff);
        document.querySelector('.js-fertile').textContent = 'du ' + jourMois(add(ovuAff, -5)) + ' au ' + jourMois(add(ovuAff, 1));
        var restant = diff(today, prochaine);
        document.querySelector('.js-jour').textContent = restant === 1 ? 'règles prévues demain' : 'règles dans ' + restant + ' jours';

        // Jours marqués sur le calendrier : 4 cycles à partir du cycle en cours
        var marques = {};
        var cle = function (d) { return d.getFullYear() + '-' + d.getMonth() + '-' + d.getDate(); };
        for (var k = 0; k < 4; k++) {
            var s = add(debut, k * C), o = add(s, C - 14);
            for (var i = -5; i <= 1; i++) marques[cle(add(o, i))] = 'fertile';
            marques[cle(o)] = 'ovu';
            for (var r = 0; r < R; r++) marques[cle(add(s, r))] = 'regles';
        }

        // Calendrier : mois en cours + mois suivant
        var cal = document.querySelector('.js-cal');
        cal.innerHTML = '';
        for (var m = 0; m < 2; m++) {
            var premier = new Date(today.getFullYear(), today.getMonth() + m, 1);
            var bloc = document.createElement('div');
            bloc.className = 'cy-month';
            var html = '<p class="cy-month__title">' + premier.toLocaleDateString('fr-FR', { month: 'long', year: 'numeric' }) + '</p><div class="cy-grid">';
            ['L', 'M', 'M', 'J', 'V', 'S', 'D'].forEach(function (j) { html += '<span class="cy-head">' + j + '</span>'; });
            var decal = (premier.getDay() + 6) % 7;   // lundi = 0
            for (var b = 0; b < decal; b++) html += '<span></span>';
            var nb = new Date(premier.getFullYear(), premier.getMonth() + 1, 0).getDate();
            for (var d = 1; d <= nb; d++) {
                var date = new Date(premier.getFullYear(), premier.getMonth(), d);
                var t = marques[cle(date)] || '';
                html += '<span class="cy-day' + (t ? ' cy-day--' + t : '') + (diff(date, today) === 0 ? ' is-today' : '') + '">' + d + '</span>';
            }
            bloc.innerHTML = html + '</div>';
            cal.appendChild(bloc);
        }

        // Les 3 prochains cycles
        var ol = document.querySelector('.js-steps');
        ol.innerHTML = '';
        for (var c = 1; c <= 3; c++) {
            var st = add(debut, c * C), ov = add(st, C - 14);
            var li = document.createElement('li');
            if (c === 1) li.className = 'is-next';
            li.innerHTML = '<span class="timeline__dot"><i class="bi bi-calendar2-heart" aria-hidden="true"></i></span><div><div class="timeline__head"><span></span><span class="timeline__date"></span></div><ul><li></li><li></li></ul></div>';
            li.querySelector('.timeline__head span').textContent = 'Règles le ' + court(st);
            li.querySelector('.timeline__date').textContent = 'Cycle ' + c;
            var lis = li.querySelectorAll('ul li');
            lis[0].textContent = 'Ovulation estimée : ' + court(ov);
            lis[1].textContent = 'Période fertile : du ' + jourMois(add(ov, -5)) + ' au ' + jourMois(add(ov, 1));
            ol.appendChild(li);
        }

        // Cycle hors des repères habituels
        var note = document.querySelector('.js-note p');
        var base = 'Ce calendrier est une estimation : l\'ovulation peut varier d\'un mois à l\'autre. <strong>Ce n\'est pas une méthode de contraception</strong> et il ne garantit pas une grossesse.';
        note.innerHTML = (C > 35) ? base + ' Un cycle de plus de 35 jours mérite d\'en parler à un médecin.' : base;

        psShowResult();
    });
});
</script>
@endpush
