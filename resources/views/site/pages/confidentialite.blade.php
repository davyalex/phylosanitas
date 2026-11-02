@extends('site.layout')
@section('title', 'Politique de confidentialité')
@section('description', 'Comment ' . ($site['nom'] ?? config('app.name')) . ' utilise vos données et les cookies (mesure d\'audience, publicité Google AdSense), et comment exercer vos droits.')

@php
    $nom     = $site['nom'] ?? config('app.name');
    $email   = $site['emails'][0]['value'] ?? null;
    $adresse = $site['adresse'] ?? null;
    $maj     = '2 novembre 2026';
@endphp

@section('content')
<section class="section section--tight">
    <div class="container-xl">

        <nav aria-label="Fil d'Ariane" class="crumbs">
            <a href="{{ route('accueil') }}">Accueil</a>
            <i class="bi bi-chevron-right" aria-hidden="true"></i>
            <span aria-current="page">Politique de confidentialité</span>
        </nav>

        <article class="legal">
            <header class="legal__head">
                <h1 class="page-title">Politique de confidentialité</h1>
                <p class="lede">Ce que {{ $nom }} fait de vos données, quels cookies sont utilisés, et comment garder le contrôle.</p>
                <p class="legal__date"><i class="bi bi-calendar-check" aria-hidden="true"></i> Dernière mise à jour : {{ $maj }}</p>
            </header>

            <nav class="legal__toc" aria-label="Sommaire">
                <strong>Sommaire</strong>
                <ol>
                    <li><a href="#responsable">Qui est responsable ?</a></li>
                    <li><a href="#donnees">Les données collectées</a></li>
                    <li><a href="#cookies">Cookies et publicité</a></li>
                    <li><a href="#duree">Durée de conservation</a></li>
                    <li><a href="#droits">Vos droits</a></li>
                    <li><a href="#contact">Nous contacter</a></li>
                </ol>
            </nav>

            <div class="prose legal__body">

                <h2 id="responsable">1. Qui est responsable de vos données ?</h2>
                <p>
                    Le site <strong>{{ $nom }}</strong> ({{ preg_replace('#^https?://#', '', url('/')) }}) est un blog d'information santé rédigé par un médecin.
                    Il est responsable des données collectées sur le site{{ $adresse ? ', basé à ' . $adresse : '' }}.
                </p>
                <p>
                    Nous ne vendons pas vos données personnelles. Nous ne collectons <strong>aucune donnée médicale</strong> :
                    le site ne propose ni consultation ni dossier patient.
                </p>

                <h2 id="donnees">2. Les données que nous collectons</h2>
                <p>Uniquement ce que vous choisissez de nous transmettre, et quelques données techniques nécessaires au fonctionnement du site.</p>
                <table>
                    <thead>
                        <tr><th>Quand ?</th><th>Quelles données ?</th><th>Pourquoi ?</th></tr>
                    </thead>
                    <tbody>
                        <tr><td>Formulaire de contact</td><td>Nom, adresse e-mail, sujet, message</td><td>Vous répondre</td></tr>
                        <tr><td>Inscription à la newsletter</td><td>Adresse e-mail</td><td>Vous envoyer nos nouveaux articles</td></tr>
                        <tr><td>Commentaire</td><td>Nom, message</td><td>Publier votre commentaire sous l'article (il est <strong>public</strong>)</td></tr>
                        <tr><td>Vote à un sondage</td><td>Votre réponse, liée de façon anonyme à votre navigateur</td><td>Afficher les résultats et éviter les votes en double</td></tr>
                        <tr><td>Navigation</td><td>Pages consultées, type d'appareil, pays (données anonymisées)</td><td>Compter les visites et améliorer le site</td></tr>
                        <tr><td>Sécurité</td><td>Adresse IP, en cas d'envoi suspect</td><td>Bloquer les robots et le spam</td></tr>
                    </tbody>
                </table>
                <p>
                    <strong>Conseil :</strong> les commentaires étant publics, n'y indiquez pas d'informations personnelles sur votre santé.
                    Pour une question personnelle, utilisez plutôt le <a href="{{ route('contact') }}">formulaire de contact</a>.
                </p>

                <h2 id="cookies">3. Cookies et publicité</h2>
                <p>
                    Un cookie est un petit fichier enregistré par votre navigateur. Le site en utilise trois sortes :
                </p>
                <table>
                    <thead>
                        <tr><th>Type</th><th>À quoi ça sert ?</th><th>Durée</th></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Indispensables</strong><br><small>{{ config('session.cookie') }}, XSRF-TOKEN</small></td>
                            <td>Faire fonctionner le site et sécuriser les formulaires. Ils ne servent pas à vous suivre.</td>
                            <td>La durée de votre visite</td>
                        </tr>
                        <tr>
                            <td><strong>Mesure d'audience</strong><br><small>Google Analytics (_ga…)</small></td>
                            <td>Savoir combien de personnes lisent quels articles, de façon statistique.</td>
                            <td>Jusqu'à 13 mois</td>
                        </tr>
                        <tr>
                            <td><strong>Publicité</strong><br><small>Google AdSense</small></td>
                            <td>Afficher les annonces qui financent le site, mesurer leur efficacité et, si vous l'acceptez, les adapter à vos centres d'intérêt.</td>
                            <td>Jusqu'à 13 mois</td>
                        </tr>
                    </tbody>
                </table>

                <h3>La publicité Google</h3>
                <p>
                    Le site est financé en partie par des annonces diffusées par <strong>Google AdSense</strong>.
                    Google, en tant que fournisseur tiers, utilise des cookies pour diffuser des annonces en fonction de vos visites
                    sur ce site et sur d'autres sites Internet.
                </p>
                <ul>
                    <li>
                        Vous pouvez désactiver les annonces personnalisées dans les
                        <a href="https://adssettings.google.com" target="_blank" rel="noopener noreferrer">paramètres des annonces Google</a>.
                        Les annonces restent affichées, mais ne sont plus adaptées à vos centres d'intérêt.
                    </li>
                    <li>
                        Pour en savoir plus :
                        <a href="https://policies.google.com/technologies/ads?hl=fr" target="_blank" rel="noopener noreferrer">comment Google utilise les cookies publicitaires</a>
                        et <a href="https://policies.google.com/technologies/partner-sites?hl=fr" target="_blank" rel="noopener noreferrer">comment Google utilise les données des sites partenaires</a>.
                    </li>
                    <li>
                        Si vous visitez le site depuis l'Europe, un message vous demande votre accord avant tout cookie publicitaire.
                        Vous pouvez changer d'avis à tout moment.
                    </li>
                </ul>
                <p>
                    Les bannières de nos partenaires locaux (pharmacies, cliniques…) sont de simples images : elles ne déposent aucun cookie.
                    Nous comptons seulement, de façon anonyme, leurs affichages et les clics.
                </p>

                <h3>Refuser les cookies</h3>
                <p>
                    Vous pouvez à tout moment bloquer ou supprimer les cookies dans les réglages de votre navigateur.
                    Le site reste consultable, mais certaines fonctions (envoi de formulaires, vote) pourraient ne plus marcher.
                </p>

                <h2 id="duree">4. Combien de temps gardons-nous vos données ?</h2>
                <ul>
                    <li><strong>Messages de contact :</strong> 3 ans au plus après notre dernier échange.</li>
                    <li><strong>Newsletter :</strong> jusqu'à votre désinscription.</li>
                    <li><strong>Commentaires :</strong> tant que l'article est en ligne, ou jusqu'à ce que vous demandiez leur suppression.</li>
                    <li><strong>Journaux de sécurité :</strong> quelques semaines.</li>
                </ul>

                <h2 id="droits">5. Vos droits</h2>
                <p>
                    Conformément à la loi ivoirienne n° 2013-450 relative à la protection des données à caractère personnel
                    (et au RGPD pour les visiteurs de l'Union européenne), vous pouvez à tout moment :
                </p>
                <ul>
                    <li>savoir quelles données nous avons sur vous et en obtenir une copie ;</li>
                    <li>les faire corriger ou supprimer ;</li>
                    <li>vous désinscrire de la newsletter ;</li>
                    <li>vous opposer à l'utilisation de vos données.</li>
                </ul>
                <p>
                    Il suffit de nous écrire (voir ci-dessous). Nous répondons dans un délai d'un mois.
                    Si vous estimez que vos droits ne sont pas respectés, vous pouvez saisir l'<strong>ARTCI</strong>
                    (Autorité de Régulation des Télécommunications/TIC de Côte d'Ivoire).
                </p>

                <h2 id="contact">6. Nous contacter</h2>
                <p>
                    Pour toute question sur vos données :
                    @if($email)
                        écrivez à <a href="mailto:{{ $email }}">{{ $email }}</a> ou
                    @endif
                    utilisez le <a href="{{ route('contact') }}">formulaire de contact</a>.
                </p>
                <p>
                    Cette page peut évoluer, par exemple si nous ajoutons un nouveau service.
                    La date de dernière mise à jour figure en haut de la page.
                </p>
            </div>
        </article>
    </div>
</section>
@endsection
