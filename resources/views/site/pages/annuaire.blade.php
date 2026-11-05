@php
    $types   = \App\Models\Etablissement::TYPES;
    $filtre  = $type || $ville || $q;
    $titreH1 = $type ? $types[$type]['pluriel'] : 'Annuaire santé';
    if ($ville) $titreH1 .= ' à ' . $ville;
@endphp

@extends('site.layout')
@section('title', $type === 'pharmacie' && !$q ? 'Pharmacies de garde et pharmacies' . ($ville ? ' à ' . $ville : '') : $titreH1)
@section('description', 'Pharmacies de garde, hôpitaux, cliniques, laboratoires et numéros d\'urgence' . ($ville ? ' à ' . $ville : '') . ' : adresses, téléphones et horaires.')
@if($q || $etablissements->currentPage() > 1)
    @section('robots', 'noindex, follow')
@endif

@section('content')
<x-svc-hero icon="geo-alt" :titre="$titreH1" :parent="$filtre ? ['Annuaire santé' => route('annuaire')] : []">
    Pharmacies de garde, hôpitaux, cliniques et numéros d'urgence : trouvez vite où vous soigner.
</x-svc-hero>

<section class="svc-body">
    <div class="container-xl">

        {{-- Numéros d'urgence --}}
        @if($urgences->count())
            <h2 class="visually-hidden">Numéros d'urgence</h2>
            <div class="urgences">
                @foreach($urgences as $u)
                    <a href="{{ \App\Models\Etablissement::telHref($u->telephone) }}" class="urgence">
                        <span class="urgence__num">{{ $u->telephone }}</span>
                        <span><strong>{{ $u->nom }}</strong>@if($u->notes)<small>{{ $u->notes }}</small>@endif</span>
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Pharmacies de garde --}}
        @if($deGarde->count() && (!$type || $type === 'pharmacie'))
            <section class="garde" aria-labelledby="garde-title">
                <div class="garde__head">
                    <h2 class="garde__title" id="garde-title"><i class="bi bi-moon-stars-fill" aria-hidden="true"></i>Pharmacies de garde{{ $ville ? ' à ' . $ville : '' }}</h2>
                    @php $fin = $deGarde->min('garde_fin'); @endphp
                    <span class="garde__dates">Jusqu'au {{ $fin->translatedFormat('l d F') }} · <a href="{{ route('garde', array_filter(['ville' => $ville])) }}" class="text-white fw-bold">Page des gardes <i class="bi bi-arrow-right" aria-hidden="true"></i></a></span>
                </div>
                <div class="place-grid place-grid--3">
                    @foreach($deGarde as $e)
                        @include('site.pages.partials.place', ['e' => $e])
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Filtres --}}
        <form method="GET" action="{{ route('annuaire') }}" class="filters" role="search">
            <div class="field">
                <label for="an-q">Nom ou quartier</label>
                <input id="an-q" type="search" name="q" value="{{ $q }}" placeholder="Ex. : Cocody, pharmacie du marché…">
            </div>
            <div class="field">
                <label for="an-type">Type</label>
                <select id="an-type" name="type">
                    <option value="">Tous</option>
                    @foreach($types as $key => $t)
                        @continue($key === 'urgence')
                        <option value="{{ $key }}" {{ $type === $key ? 'selected' : '' }}>{{ $t['pluriel'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label for="an-ville">Ville</label>
                <select id="an-ville" name="ville">
                    <option value="">Toutes</option>
                    @foreach($villes as $v)
                        <option value="{{ $v }}" {{ $ville === $v ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-medical"><i class="bi bi-search" aria-hidden="true"></i> Chercher</button>
        </form>

        <nav class="type-chips" aria-label="Types d'établissements">
            <a href="{{ route('annuaire', array_filter(['ville' => $ville])) }}" class="chip {{ !$type ? 'is-active' : '' }}">Tous</a>
            @foreach($types as $key => $t)
                @continue($key === 'urgence' || !($parType[$key] ?? 0))
                <a href="{{ route('annuaire', array_filter(['type' => $key, 'ville' => $ville])) }}" class="chip {{ $type === $key ? 'is-active' : '' }}">
                    <i class="bi bi-{{ $t['icon'] }}" aria-hidden="true"></i>{{ $t['pluriel'] }}<span class="chip__count">{{ $parType[$key] }}</span>
                </a>
            @endforeach
        </nav>

        @if($etablissements->isEmpty())
            <div class="empty">
                <i class="bi bi-geo-alt" aria-hidden="true"></i>
                @if($filtre)
                    <h2>Aucun résultat</h2>
                    <p>Essayez une autre ville ou un autre type d'établissement.</p>
                    <a href="{{ route('annuaire') }}" class="btn btn-outline-medical">Voir tout l'annuaire</a>
                @else
                    <h2>L'annuaire se remplit</h2>
                    <p>Notre équipe ajoute les établissements de santé au fur et à mesure. En cas d'urgence, appelez le {{ $site['urgence'] }}.</p>
                @endif
            </div>
        @else
            <div class="place-grid place-grid--3">
                @foreach($etablissements as $e)
                    @include('site.pages.partials.place', ['e' => $e])
                @endforeach
            </div>
            <div class="pager-wrap">{!! $etablissements->links('vendor.pagination.custom') !!}</div>
        @endif

        <aside class="disclaimer" aria-label="Avertissement">
            <i class="bi bi-info-circle" aria-hidden="true"></i>
            <p>Les informations (gardes, horaires, numéros) peuvent changer. Appelez avant de vous déplacer. Une erreur ? <a href="{{ route('contact') }}">Signalez-la-nous</a>.</p>
        </aside>
    </div>
</section>
@endsection
