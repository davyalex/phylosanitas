@extends('site.pages.outils._base')

@section('tool_form')
    <div class="field">
        <label for="hy-poids">Votre poids</label>
        <div class="field__unit"><input id="hy-poids" type="number" inputmode="decimal" min="25" max="250" step="0.1" required placeholder="70"><span>kg</span></div>
    </div>

    <fieldset class="field">
        <legend class="fs-6 fw-semibold mb-2" style="font-size: var(--fs-sm) !important">Activité physique du jour</legend>
        <div class="choice-row">
            <label><input type="radio" name="activite" value="0" checked> Peu ou pas d'activité</label>
            <label><input type="radio" name="activite" value="500"> 30 min à 1 h (marche rapide, ménage, vélo…)</label>
            <label><input type="radio" name="activite" value="1000"> Plus d'1 h ou travail physique</label>
        </div>
    </fieldset>

    <fieldset class="field">
        <legend class="fw-semibold mb-2" style="font-size: var(--fs-sm)">Temps qu'il fait</legend>
        <div class="choice-row choice-row--2">
            <label><input type="radio" name="chaleur" value="0"> Tempéré</label>
            <label><input type="radio" name="chaleur" value="500" checked> Chaud / humide</label>
        </div>
    </fieldset>

    <fieldset class="field">
        <legend class="fw-semibold mb-2" style="font-size: var(--fs-sm)">Situation</legend>
        <div class="choice-row">
            <label><input type="radio" name="situation" value="0" checked> Aucune particulière</label>
            <label><input type="radio" name="situation" value="300"> Enceinte</label>
            <label><input type="radio" name="situation" value="700"> J'allaite</label>
        </div>
    </fieldset>
    <p class="field__error js-err" hidden></p>
@endsection

@section('tool_result')
    <h2>À boire aujourd'hui</h2>
    <div class="big-figure"><span class="js-litres">0</span><small>litres</small></div>
    <div class="water js-verres" aria-hidden="true"></div>
    <p class="mb-0">Soit environ <strong class="js-nb-verres"></strong> verres de 25 cl, à répartir dans la journée.</p>
    <ul class="facts">
        <li><span>Base selon votre poids</span><strong class="js-base"></strong></li>
        <li><span>Activité, chaleur, situation</span><strong class="js-extra"></strong></li>
    </ul>
    <div class="tool-note"><i class="bi bi-exclamation-triangle" aria-hidden="true"></i>
        <p>Maladie du cœur ou des reins : la quantité d'eau peut devoir être limitée. Suivez l'avis de votre médecin.</p></div>
@endsection

@section('tool_explain')
    <h2>Bien s'hydrater au quotidien</h2>
    <div class="prose">
        <p>Le corps perd de l'eau en permanence : respiration, transpiration, urines. Avec la chaleur et l'effort, ces pertes augmentent fortement. Le calcul part d'environ 30 ml par kilo de poids, puis ajoute un supplément pour l'activité, la chaleur, la grossesse ou l'allaitement.</p>
        <ul>
            <li>Buvez régulièrement, sans attendre d'avoir soif, surtout les personnes âgées et les enfants.</li>
            <li>Préférez l'eau potable. Limitez les boissons sucrées et les sodas.</li>
            <li>Des urines claires sont un bon signe ; des urines foncées signalent souvent un manque d'eau.</li>
            <li>Diarrhée ou vomissements : utilisez les sels de réhydratation orale (SRO) et consultez si cela dure, surtout chez l'enfant.</li>
        </ul>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.querySelector('.js-tool'), err = form.querySelector('.js-err');
    var val = function (n) { return parseInt((form.querySelector('input[name="' + n + '"]:checked') || {}).value || 0, 10); };
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        var p = parseFloat(document.getElementById('hy-poids').value.replace(',', '.'));
        if (!(p >= 25 && p <= 250)) { err.textContent = 'Indiquez un poids entre 25 et 250 kg.'; err.hidden = false; return; }
        err.hidden = true;
        var base = p * 30, extra = val('activite') + val('chaleur') + val('situation');
        var total = Math.min(base + extra, 5000);
        var litres = Math.round(total / 100) / 10, verres = Math.round(total / 250);
        document.querySelector('.js-litres').textContent = psFmt(litres, 1);
        document.querySelector('.js-nb-verres').textContent = verres;
        document.querySelector('.js-base').textContent = psFmt(base / 1000, 1) + ' L';
        document.querySelector('.js-extra').textContent = '+ ' + psFmt(extra / 1000, 1) + ' L';
        var box = document.querySelector('.js-verres');
        box.innerHTML = '';
        for (var i = 0; i < verres; i++) box.insertAdjacentHTML('beforeend', '<i class="bi bi-cup-straw"></i>');
        psShowResult();
    });
});
</script>
@endpush
