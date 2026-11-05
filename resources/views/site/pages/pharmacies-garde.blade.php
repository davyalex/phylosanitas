@php
    $fmt = fn($d) => $d ? \Carbon\Carbon::parse($d)->translatedFormat('l j F') : null;
    $periode = $debut && $fin ? 'du ' . $fmt($debut) . ' au ' . $fmt($fin) : null;
    $lieu = $ville ? ' à ' . $ville : '';
    $url  = route('garde', array_filter(['ville' => request()->query('ville') ? $ville : null]));

    // Texte prêt à partager sur WhatsApp (40 pharmacies max pour rester lisible)
    $partage = '🌙 Pharmacies de garde' . $lieu . ($periode ? ' ' . $periode : '') . "\n\n"
        . $gardes->take(40)->map(fn($e) => '• ' . $e->nom . ($e->quartier ? ' (' . $e->quartier . ')' : '') . ($e->telephone ? ' — ' . $e->telephone : ''))->implode("\n")
        . ($gardes->count() > 40 ? "\n…" : '')
        . "\n\nListe complète : " . $url;
@endphp

@extends('site.layout')
@section('title', 'Pharmacies de garde' . $lieu . ($periode ? ' ' . $periode : ' cette semaine'))
@section('description', 'Liste des pharmacies de garde' . $lieu . ($periode ? ' ' . $periode : ' cette semaine') . ' : adresses, téléphones et itinéraires. Mise à jour chaque semaine.')
@section('canonical', $url)

@push('jsonld')
@if($gardes->count())
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type'    => 'ItemList',
    'name'     => 'Pharmacies de garde' . $lieu,
    'numberOfItems' => $gardes->count(),
    'itemListElement' => $gardes->values()->map(fn($e, $i) => [
        '@type'    => 'ListItem',
        'position' => $i + 1,
        'item'     => array_filter([
            '@type'     => 'Pharmacy',
            'name'      => $e->nom,
            'telephone' => $e->telephone,
            'address'   => array_filter(['@type' => 'PostalAddress', 'streetAddress' => $e->adresse, 'addressLocality' => collect([$e->quartier, $e->ville])->filter()->implode(', '), 'addressCountry' => 'CI']),
            'geo'       => $e->latitude !== null ? ['@type' => 'GeoCoordinates', 'latitude' => $e->latitude, 'longitude' => $e->longitude] : null,
        ]),
    ])->all(),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endif
@endpush

@section('content')
<x-svc-hero icon="moon-stars" :titre="'Pharmacies de garde' . $lieu">
    @if($periode)
        Ouvertes la nuit, le dimanche et les jours fériés, {{ $periode }}.
    @else
        Les pharmacies ouvertes la nuit, le dimanche et les jours fériés.
    @endif
    <x-slot:extra>
        <div class="svc-hero__meta">
            <span><i class="bi bi-capsule" aria-hidden="true"></i>{{ $gardes->count() }} pharmacie{{ $gardes->count() > 1 ? 's' : '' }} de garde{{ $lieu }}</span>
            <span><i class="bi bi-arrow-repeat" aria-hidden="true"></i>Liste mise à jour chaque semaine</span>
        </div>
    </x-slot:extra>
</x-svc-hero>

<section class="svc-body">
    <div class="container-xl">

        @if($gardes->isEmpty() && !$ville)
            <div class="empty">
                <i class="bi bi-moon-stars" aria-hidden="true"></i>
                <h2>La liste de cette semaine arrive bientôt</h2>
                <p>Notre équipe met à jour les pharmacies de garde chaque semaine. Revenez dans quelques heures.</p>
                <div class="d-flex flex-wrap gap-2 justify-content-center">
                    @if(config('sante.modules.annuaire'))<a href="{{ route('annuaire', ['type' => 'pharmacie']) }}" class="btn btn-medical">Voir toutes les pharmacies</a>@endif
                    <a href="{{ $site['urgence_href'] }}" class="btn btn-outline-medical"><i class="bi bi-telephone" aria-hidden="true"></i> Urgence : {{ $site['urgence'] }}</a>
                </div>
            </div>
        @else
            {{-- Ville (rechargement, adresse partageable) + recherche instantanée + partage --}}
            <div class="garde-tools">
                <div class="search-field" role="search">
                    <i class="bi bi-search" aria-hidden="true"></i>
                    <label for="garde-q" class="visually-hidden">Rechercher une commune, un quartier ou une pharmacie</label>
                    <input id="garde-q" type="search" class="js-garde-filter" placeholder="Commune, quartier ou nom : Cocody, Yopougon, Plateau…" autocomplete="off">
                </div>
                @if($villes->count() > 1)
                    <form method="GET" action="{{ route('garde') }}" class="garde-ville">
                        <label for="garde-ville" class="visually-hidden">Ville</label>
                        <i class="bi bi-geo-alt" aria-hidden="true"></i>
                        <select id="garde-ville" name="ville" onchange="this.form.submit()">
                            <option value="">Toutes les villes ({{ $toutes->count() }})</option>
                            @foreach($villes as $v)
                                <option value="{{ $v }}" {{ $ville === $v ? 'selected' : '' }}>{{ $v }} ({{ $toutes->where('ville', $v)->count() }})</option>
                            @endforeach
                        </select>
                        <noscript><button type="submit" class="btn btn-outline-medical btn-sm">OK</button></noscript>
                    </form>
                @endif
                <a href="https://wa.me/?text={{ rawurlencode($partage) }}" target="_blank" rel="noopener" class="btn btn-whatsapp"><i class="bi bi-whatsapp" aria-hidden="true"></i>Partager</a>
            </div>

            <p class="garde-count" role="status" aria-live="polite"><strong class="js-garde-count">{{ $gardes->count() }}</strong> pharmacie{{ $gardes->count() > 1 ? 's' : '' }} de garde{{ $lieu }}</p>

            <div class="gtable-wrap">
                <table class="gtable">
                    <thead>
                        <tr>
                            <th scope="col">Pharmacie</th>
                            <th scope="col">Téléphone</th>
                            <th scope="col"><span class="visually-hidden">Itinéraire</span></th>
                        </tr>
                    </thead>
                    @foreach($parQuartier as $quartier => $liste)
                        <tbody class="js-garde-group">
                            <tr class="gtable__group">
                                <th colspan="3" scope="rowgroup"><i class="bi bi-geo-alt-fill" aria-hidden="true"></i>{{ $quartier }}@if(!$ville && $liste->first()->ville && $liste->first()->ville !== $quartier)<span>{{ $liste->first()->ville }}</span>@endif</th>
                            </tr>
                            @foreach($liste as $e)
                                @php $carte = $e->carteUrl(); @endphp
                                <tr class="js-garde-item" data-search="{{ Str::lower(Str::ascii($e->nom . ' ' . $e->quartier . ' ' . $e->ville . ' ' . $e->adresse)) }}">
                                    <td class="gtable__nom">{{ $e->nom }}@if($e->adresse)<small>{{ $e->adresse }}</small>@endif</td>
                                    <td class="gtable__tel">
                                        @if($e->telephone)
                                            <a href="{{ \App\Models\Etablissement::telHref($e->telephone) }}" class="gtable__call"><i class="bi bi-telephone-fill" aria-hidden="true"></i>{{ $e->telephone }}</a>
                                        @else
                                            <span class="muted">—</span>
                                        @endif
                                    </td>
                                    <td class="gtable__map">
                                        @if($carte)<a href="{{ $carte }}" target="_blank" rel="noopener" class="gtable__mapbtn" aria-label="Itinéraire vers {{ $e->nom }}" title="Itinéraire"><i class="bi bi-map" aria-hidden="true"></i></a>@endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    @endforeach
                </table>
            </div>

            @if($src = \App\Models\Setting::get('gardes_source'))
                <p class="garde-source">Source :
                    @if($srcUrl = \App\Models\Setting::get('gardes_source_url'))
                        <a href="{{ $srcUrl }}" target="_blank" rel="noopener">{{ $src }}</a>
                    @else
                        {{ $src }}
                    @endif
                </p>
            @endif

            <div class="empty az-empty js-garde-empty">
                <i class="bi bi-search" aria-hidden="true"></i>
                <h2>Aucune pharmacie de garde ne correspond</h2>
                <p>Essayez une commune voisine{{ $ville ? ', ou choisissez « Toutes les villes »' : '' }}.</p>
            </div>
        @endif

        <aside class="disclaimer" aria-label="Avertissement">
            <i class="bi bi-info-circle" aria-hidden="true"></i>
            <p>Les gardes peuvent changer. Appelez la pharmacie avant de vous déplacer, surtout la nuit. Une erreur dans la liste ? <a href="{{ route('contact') }}">Signalez-la-nous</a>. En cas d'urgence, appelez le <a href="{{ $site['urgence_href'] }}"><strong>{{ $site['urgence'] }}</strong></a>.</p>
        </aside>

        <div class="mini-head"><h2>Autres services</h2></div>
        @include('site.pages.partials.services-grid', ['except' => 'garde', 'max' => 3])
    </div>
</section>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var input = document.querySelector('.js-garde-filter');
        if (!input) return;
        var norm = function (s) { return (s || '').toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, ''); };
        var empty = document.querySelector('.js-garde-empty');
        var count = document.querySelector('.js-garde-count');
        var table = document.querySelector('.gtable-wrap');
        input.addEventListener('input', function () {
            // Tous les mots doivent correspondre : « cocody riviera » trouve les pharmacies de la Riviera à Cocody
            var mots = norm(input.value.trim()).split(/\s+/).filter(Boolean), total = 0;
            document.querySelectorAll('.js-garde-group').forEach(function (g) {
                var visible = 0;
                g.querySelectorAll('.js-garde-item').forEach(function (it) {
                    var txt = it.getAttribute('data-search');
                    var ok = mots.every(function (m) { return txt.indexOf(m) !== -1; });
                    it.hidden = !ok;
                    if (ok) visible++;
                });
                g.hidden = visible === 0;
                total += visible;
            });
            if (count) count.textContent = total;
            if (table) table.hidden = total === 0;
            empty.classList.toggle('is-visible', total === 0);
        });
    });
</script>
@endpush
@endsection
