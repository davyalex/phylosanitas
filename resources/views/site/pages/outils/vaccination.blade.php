@extends('site.pages.outils._base')

@section('tool_form')
    <div class="field">
        <label for="vc-naissance">Date de naissance de l'enfant</label>
        <input id="vc-naissance" type="date" required max="{{ now()->toDateString() }}" min="{{ now()->subYears(15)->toDateString() }}">
    </div>
    <div class="field">
        <label for="vc-prenom">Prénom (facultatif)</label>
        <input id="vc-prenom" type="text" maxlength="40" placeholder="Pour personnaliser l'affichage" autocomplete="off">
    </div>
    <p class="field__error js-err" hidden></p>
@endsection

@section('tool_result')
    <h2 class="js-titre">Calendrier de vaccination</h2>
    <p class="mb-0 js-age"></p>
    <ol class="timeline js-steps"></ol>
    <div class="tool-note"><i class="bi bi-journal-medical" aria-hidden="true"></i>
        <p>Une date passée ne veut pas dire que le vaccin a été fait : vérifiez le carnet de santé. Un vaccin en retard peut presque toujours être rattrapé, parlez-en au centre de santé. {{ config('sante.vaccins_note') }}</p></div>
@endsection

@section('tool_explain')
    <h2>Pourquoi respecter le calendrier ?</h2>
    <div class="prose">
        <p>Les vaccins protègent l'enfant au moment où il est le plus fragile face à des maladies graves : rougeole, coqueluche, méningite, tuberculose, poliomyélite… Ils sont donnés gratuitement dans les centres de santé dans le cadre du Programme élargi de vaccination (PEV).</p>
        <p>Ce calendrier est indicatif. Le calendrier officiel peut évoluer : fiez-vous toujours au carnet de santé de l'enfant et aux conseils de l'agent de santé.</p>
        <ul>
            <li>Gardez le carnet de santé dans un endroit sûr et apportez-le à chaque visite.</li>
            <li>Une petite fièvre ou une douleur au point d'injection sont fréquentes et passent en 1 à 2 jours.</li>
            <li>Consultez si la fièvre est forte, si l'enfant pleure sans arrêt ou paraît très malade.</li>
        </ul>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var cal = @json(config('sante.vaccins'));
    var form = document.querySelector('.js-tool'), err = form.querySelector('.js-err');
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        var v = document.getElementById('vc-naissance').value;
        var prenom = document.getElementById('vc-prenom').value.trim();
        var today = new Date(); today.setHours(0, 0, 0, 0);
        if (!v) { err.textContent = 'Indiquez la date de naissance.'; err.hidden = false; return; }
        var nais = new Date(v + 'T00:00:00');
        if (nais > today) { err.textContent = 'La date de naissance ne peut pas être dans le futur.'; err.hidden = false; return; }
        err.hidden = true;

        var jours = Math.round((today - nais) / 86400000);
        var mois = Math.floor(jours / 30.44);
        document.querySelector('.js-titre').textContent = prenom ? 'Les vaccins de ' + prenom : 'Calendrier de vaccination';
        document.querySelector('.js-age').textContent = 'Âge : ' + (jours < 61 ? jours + ' jour' + (jours > 1 ? 's' : '') : mois < 24 ? mois + ' mois' : Math.floor(mois / 12) + ' ans');

        var ol = document.querySelector('.js-steps'), nextDone = false;
        ol.innerHTML = '';
        cal.forEach(function (et) {
            var date = new Date(nais); date.setDate(date.getDate() + et.jours);
            var passe = date < today;
            var li = document.createElement('li');
            if (passe) li.className = 'is-done';
            else if (!nextDone) { li.className = 'is-next'; nextDone = true; }
            li.innerHTML = '<span class="timeline__dot"><i class="bi bi-' + (passe ? 'check-lg' : 'shield-plus') + '" aria-hidden="true"></i></span><div><div class="timeline__head"><span></span><span class="timeline__date"></span></div><ul></ul></div>';
            var head = li.querySelector('.timeline__head span');
            head.textContent = et.age;
            if (li.className === 'is-next') head.insertAdjacentHTML('beforeend', '<span class="timeline__badge">Prochain</span>');
            li.querySelector('.timeline__date').textContent = date.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' });
            et.vaccins.forEach(function (n) { var x = document.createElement('li'); x.textContent = n; li.querySelector('ul').appendChild(x); });
            ol.appendChild(li);
        });
        psShowResult();
    });
});
</script>
@endpush
