@extends('site.pages.outils._base')

@section('tool_form')
    <div class="field-row">
        <div class="field">
            <label for="imc-poids">Poids</label>
            <div class="field__unit"><input id="imc-poids" type="number" inputmode="decimal" min="20" max="350" step="0.1" required placeholder="70"><span>kg</span></div>
        </div>
        <div class="field">
            <label for="imc-taille">Taille</label>
            <div class="field__unit"><input id="imc-taille" type="number" inputmode="numeric" min="100" max="250" step="1" required placeholder="170"><span>cm</span></div>
        </div>
    </div>
    <p class="field__error js-err" hidden></p>
    <p class="field__hint mb-3">Pour les adultes (18 ans et plus). Pour un enfant ou une femme enceinte, l'IMC s'interprète différemment : demandez à votre médecin.</p>
@endsection

@section('tool_result')
    <h2>Votre IMC</h2>
    <div class="big-figure js-imc">0</div>
    <span class="verdict-tag js-tag"></span>
    <div class="gauge" aria-hidden="true">
        <div class="gauge__track"><span></span><span></span><span></span><span></span><span></span><span></span></div>
        <div class="gauge__pin js-pin" style="left:0"></div>
        <div class="gauge__labels"><span>15</span><span>18,5</span><span>25</span><span>30</span><span>35</span><span>40</span><span>45</span></div>
    </div>
    <p class="js-conseil mt-3 mb-0"></p>
    <ul class="facts">
        <li><span>Poids conseillé pour votre taille</span><strong class="js-range"></strong></li>
    </ul>
@endsection

@section('tool_explain')
    <h2>Qu'est-ce que l'IMC ?</h2>
    <div class="prose">
        <p>L'indice de masse corporelle (IMC) se calcule en divisant le poids (en kg) par la taille (en mètres) au carré. C'est un repère utilisé par l'Organisation mondiale de la santé pour savoir si le poids est adapté à la taille.</p>
        <ul>
            <li><strong>Moins de 18,5</strong> : maigreur</li>
            <li><strong>18,5 à 24,9</strong> : poids normal</li>
            <li><strong>25 à 29,9</strong> : surpoids</li>
            <li><strong>30 et plus</strong> : obésité</li>
        </ul>
        <p>L'IMC ne tient pas compte de la masse musculaire ni de la répartition des graisses : un sportif très musclé peut avoir un IMC élevé sans excès de graisse. Le tour de taille est un bon complément.</p>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var seuils = @json(config('sante.imc'));
    var form = document.querySelector('.js-tool'), err = form.querySelector('.js-err');
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        var p = parseFloat(document.getElementById('imc-poids').value.replace(',', '.'));
        var t = parseFloat(document.getElementById('imc-taille').value.replace(',', '.'));
        if (!(p >= 20 && p <= 350) || !(t >= 100 && t <= 250)) {
            err.textContent = 'Indiquez un poids entre 20 et 350 kg et une taille entre 100 et 250 cm.';
            err.hidden = false; return;
        }
        err.hidden = true;
        var m = t / 100, imc = p / (m * m);
        var s = seuils.find(function (x) { return imc < x.max; }) || seuils[seuils.length - 1];
        document.querySelector('.js-imc').textContent = psFmt(imc, 1);
        var tag = document.querySelector('.js-tag');
        tag.className = 'verdict-tag verdict-tag--' + s.ton + ' js-tag';
        tag.textContent = s.label;
        document.querySelector('.js-conseil').textContent = s.conseil;
        document.querySelector('.js-pin').style.left = Math.max(0, Math.min(100, (imc - 15) / 30 * 100)) + '%';
        document.querySelector('.js-range').textContent = psFmt(18.5 * m * m) + ' à ' + psFmt(24.9 * m * m) + ' kg';
        psShowResult();
    });
});
</script>
@endpush
