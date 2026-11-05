@extends('site.layout')
@section('title', 'Maladies de A à Z')
@section('description', 'Abécédaire des maladies : symptômes, causes, prévention et quand consulter, expliqués simplement par des professionnels de santé.')

@section('content')
<x-svc-hero icon="journal-medical" titre="Maladies de A à Z">
    Symptômes, prévention et quand consulter : l'essentiel sur les maladies courantes, en mots simples.
    <x-slot:extra>
        @if($maladies->count())
            <div class="search-field" role="search">
                <i class="bi bi-search" aria-hidden="true"></i>
                <label for="az-q" class="visually-hidden">Rechercher une maladie</label>
                <input id="az-q" type="search" class="js-az-filter" placeholder="Rechercher : paludisme, tension, diabète…" autocomplete="off">
            </div>
        @endif
    </x-slot:extra>
</x-svc-hero>

<section class="svc-body pt-0">
    <div class="container-xl">
        @if($maladies->isEmpty())
            <div class="empty mt-4">
                <i class="bi bi-journal-medical" aria-hidden="true"></i>
                <h2>Les premières fiches arrivent bientôt</h2>
                <p>Notre équipe médicale prépare les fiches des maladies les plus courantes.</p>
                <a href="{{ config('sante.modules.questions') ? route('questions.index') : route('outils.index') }}" class="btn btn-medical">{{ config('sante.modules.questions') ? 'Poser une question au médecin' : 'Voir les outils santé' }}</a>
            </div>
        @else
            <nav class="az-nav" aria-label="Lettres">
                @foreach(range('A', 'Z') as $l)
                    @if($parLettre->has($l))
                        <a href="#lettre-{{ $l }}">{{ $l }}</a>
                    @else
                        <span aria-hidden="true">{{ $l }}</span>
                    @endif
                @endforeach
            </nav>

            @foreach($parLettre as $lettre => $groupe)
                <section class="az-group js-az-group" id="lettre-{{ $lettre }}" aria-labelledby="titre-{{ $lettre }}">
                    <h2 class="az-group__letter" id="titre-{{ $lettre }}">{{ $lettre }}</h2>
                    <ul class="az-list">
                        @foreach($groupe as $m)
                            <li class="js-az-item" data-search="{{ $m->nom }} {{ $m->autres_noms }}">
                                <article class="az-item">
                                    <h3><a href="{{ route('maladies.show', $m->slug) }}">{{ $m->nom }}</a></h3>
                                    @if($m->autres_noms)<small>{{ $m->autres_noms }}</small>@endif
                                    <p>{{ $m->resume }}</p>
                                </article>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endforeach

            <div class="empty az-empty js-az-empty">
                <i class="bi bi-search" aria-hidden="true"></i>
                <h2>Aucune fiche ne correspond</h2>
                <p>Essayez un autre mot{{ config('sante.modules.questions') ? ', ou posez directement votre question à notre médecin' : '' }}.</p>
                @if(config('sante.modules.questions'))<a href="{{ route('questions.index') }}" class="btn btn-medical">Poser une question</a>@endif
            </div>
        @endif

        <aside class="disclaimer" aria-label="Avertissement">
            <i class="bi bi-info-circle" aria-hidden="true"></i>
            <p>Ces fiches informent et ne remplacent pas une consultation. En cas d'urgence, appelez le <a href="{{ $site['urgence_href'] }}"><strong>{{ $site['urgence'] }}</strong></a>.</p>
        </aside>
    </div>
</section>
@endsection
