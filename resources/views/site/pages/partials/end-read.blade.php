{{-- Fin de lecture : remerciement + prochaines étapes + rappel urgence --}}
<section class="endread" aria-labelledby="endread-title">
    <x-health-art name="read" class="endread__art" />
    <div class="endread__body">
        <h2 id="endread-title" class="endread__title">Merci pour votre lecture</h2>
        <p class="endread__text">Besoin de soins ? Trouvez une pharmacie de garde près de chez vous. En cas d'urgence, appelez le <a href="{{ $site['urgence_href'] ?? 'tel:185' }}"><strong>{{ $site['urgence'] ?? '185' }}</strong></a>.</p>
        <div class="endread__actions">
            <a href="{{ route('garde') }}" class="btn btn-medical"><i class="bi bi-moon-stars" aria-hidden="true"></i> Pharmacies de garde</a>
            <a href="{{ route('post.list') }}" class="btn btn-outline-medical">Lire un autre article</a>
        </div>
    </div>
    <ul class="endread__tips" aria-label="Rappels santé">
        <li><x-health-art name="food" /><span>Mangez équilibré</span></li>
        <li><x-health-art name="move" /><span>Bougez chaque jour</span></li>
        <li><x-health-art name="vaccine" /><span>Vaccins à jour</span></li>
    </ul>
</section>
