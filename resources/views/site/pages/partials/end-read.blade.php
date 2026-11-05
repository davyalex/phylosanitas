{{-- Fin de lecture : remerciement + prochaines étapes + rappel urgence --}}
<section class="endread" aria-labelledby="endread-title">
    <x-health-art name="read" class="endread__art" />
    <div class="endread__body">
        <h2 id="endread-title" class="endread__title">Merci pour votre lecture</h2>
        @if(config('sante.modules.questions'))
            <p class="endread__text">Une question sur votre situation ? Le médecin vous répond, gratuitement et de façon anonyme. En cas d'urgence, appelez le <a href="{{ $site['urgence_href'] ?? 'tel:185' }}"><strong>{{ $site['urgence'] ?? '185' }}</strong></a>.</p>
        @else
            <p class="endread__text">Besoin de soins ? Trouvez une pharmacie de garde près de chez vous. En cas d'urgence, appelez le <a href="{{ $site['urgence_href'] ?? 'tel:185' }}"><strong>{{ $site['urgence'] ?? '185' }}</strong></a>.</p>
        @endif
        <div class="endread__actions">
            @if(config('sante.modules.questions'))
                <a href="{{ route('questions.index') }}#poser" class="btn btn-medical"><i class="bi bi-chat-heart" aria-hidden="true"></i> Poser une question</a>
            @else
                <a href="{{ route('garde') }}" class="btn btn-medical"><i class="bi bi-moon-stars" aria-hidden="true"></i> Pharmacies de garde</a>
            @endif
            <a href="{{ route('post.list') }}" class="btn btn-outline-medical">Lire un autre article</a>
        </div>
        @if(config('sante.modules.notifications'))
        <div class="notify-card js-push-card" hidden>
            <span class="notify-card__icon" aria-hidden="true"><i class="bi bi-bell"></i></span>
            <div class="notify-card__body">
                <strong class="js-push-title">Ne manquez aucun conseil santé</strong>
                <span class="js-push-text">Recevez une notification à chaque nouvel article, même site fermé.</span>
            </div>
            <button type="button" class="btn btn-medical btn-sm js-push-toggle">Activer</button>
        </div>
        @endif
    </div>
    <ul class="endread__tips" aria-label="Rappels santé">
        <li><x-health-art name="food" /><span>Mangez équilibré</span></li>
        <li><x-health-art name="move" /><span>Bougez chaque jour</span></li>
        <li><x-health-art name="vaccine" /><span>Vaccins à jour</span></li>
    </ul>
</section>
