@php
    $url  = route('maladies.show', $maladie->slug);
    $desc = Str::limit($maladie->resume, 155, '…');
    $rubriques = collect(\App\Models\Maladie::RUBRIQUES)->filter(fn($r, $champ) => filled($maladie->$champ));
    $relu = $maladie->reviewer && $maladie->reviewer->profil_public ? $maladie->reviewer : null;
@endphp

@extends('site.layout')
@section('title', $maladie->nom . ' : symptômes, prévention, quand consulter')
@section('description', $desc)
@section('canonical', $url)

@push('jsonld')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@graph'   => array_values(array_filter([
        array_filter([
            '@type'       => 'MedicalWebPage',
            '@id'         => $url . '#page',
            'url'         => $url,
            'name'        => $maladie->nom,
            'description' => $desc,
            'inLanguage'  => 'fr-FR',
            'lastReviewed'=> $maladie->updated_at?->toDateString(),
            'reviewedBy'  => $relu ? ['@type' => 'Person', 'name' => $relu->nom_public, 'url' => route('auteur.show', $relu->slug)] : null,
            'about'       => array_filter([
                '@type'         => 'MedicalCondition',
                'name'          => $maladie->nom,
                'alternateName' => $maladie->autres_noms ? array_map('trim', explode(',', $maladie->autres_noms)) : null,
                'signOrSymptom' => collect(\App\Models\Maladie::lignes($maladie->symptomes))->map(fn($s) => ['@type' => 'MedicalSignOrSymptom', 'name' => $s])->all() ?: null,
            ]),
            'publisher'   => ['@id' => url('/') . '#organization'],
        ]),
        [
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Accueil', 'item' => route('accueil')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Maladies de A à Z', 'item' => route('maladies.index')],
                ['@type' => 'ListItem', 'position' => 3, 'name' => $maladie->nom],
            ],
        ],
    ])),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@section('content')
<x-svc-hero icon="journal-medical" :titre="$maladie->nom" :parent="['Maladies de A à Z' => route('maladies.index')]">
    @if($maladie->autres_noms)Aussi appelée : {{ $maladie->autres_noms }}@endif
    <x-slot:extra>
        <div class="svc-hero__meta">
            <span><i class="bi bi-arrow-repeat" aria-hidden="true"></i>Mis à jour le {{ $maladie->updated_at->translatedFormat('d F Y') }}</span>
            @if($relu)<span><i class="bi bi-patch-check-fill" aria-hidden="true"></i>Relu par <a href="{{ route('auteur.show', $relu->slug) }}" class="text-white fw-semibold">{{ $relu->nom_public }}</a></span>@endif
        </div>
        @if($rubriques->count() > 1)
            <nav class="fiche__toc" aria-label="Sommaire">
                @foreach($rubriques as $champ => [$titre, $icone])
                    <a href="#{{ $champ }}"><i class="bi bi-{{ $icone }}" aria-hidden="true"></i>{{ $titre }}</a>
                @endforeach
            </nav>
        @endif
    </x-slot:extra>
</x-svc-hero>

<section class="svc-body">
    <div class="container-xl">
        <div class="layout">
            <div class="layout__main">
                <article class="fiche">
                    <div class="fiche__summary"><p>{{ $maladie->resume }}</p></div>

                    @foreach($rubriques as $champ => [$titre, $icone])
                        @php $lignes = \App\Models\Maladie::lignes($maladie->$champ); @endphp
                        <section class="fiche__sec {{ $champ === 'quand_consulter' ? 'fiche__sec--alert' : '' }}" id="{{ $champ }}" aria-labelledby="h-{{ $champ }}">
                            <h2 id="h-{{ $champ }}"><i class="bi bi-{{ $icone }}" aria-hidden="true"></i>{{ $titre }}</h2>
                            @if($champ === 'traitement' || count($lignes) === 1)
                                @foreach($lignes as $l)<p>{{ $l }}</p>@endforeach
                            @else
                                <ul>@foreach($lignes as $l)<li>{{ $l }}</li>@endforeach</ul>
                            @endif
                        </section>
                    @endforeach

                    <aside class="notice" style="margin-top:1.5rem" aria-label="Avertissement médical">
                        <i class="bi bi-info-circle" aria-hidden="true"></i>
                        <p>Cette fiche informe et ne remplace pas une consultation. En cas d'urgence, appelez le <a href="{{ $site['urgence_href'] }}"><strong>{{ $site['urgence'] }}</strong></a>.</p>
                    </aside>

                    <footer class="share">
                        <span class="share__label">Partager</span>
                        <a href="https://wa.me/?text={{ urlencode($maladie->nom . ' : symptômes, prévention, quand consulter ' . $url) }}" target="_blank" rel="noopener noreferrer" class="share__btn" aria-label="Partager sur WhatsApp"><i class="bi bi-whatsapp"></i></a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($url) }}" target="_blank" rel="noopener noreferrer" class="share__btn" aria-label="Partager sur Facebook"><i class="bi bi-facebook"></i></a>
                        <button type="button" class="share__btn js-copy-link" data-url="{{ $url }}" aria-label="Copier le lien"><i class="bi bi-link-45deg" aria-hidden="true"></i></button>
                        <span class="share__feedback" role="status" aria-live="polite"></span>
                    </footer>
                </article>

                @if(config('sante.modules.questions'))
                    <div class="notify-card" style="margin-top:1.5rem">
                        <span class="notify-card__icon" aria-hidden="true"><i class="bi bi-chat-heart"></i></span>
                        <div class="notify-card__body">
                            <strong>Une question sur votre situation ?</strong>
                            <span>Écrivez-nous : un professionnel de santé vous répond, de façon anonyme.</span>
                        </div>
                        <a href="{{ route('questions.index') }}#poser" class="btn btn-medical btn-sm">Poser ma question</a>
                    </div>
                @else
                    <div class="notify-card" style="margin-top:1.5rem">
                        <span class="notify-card__icon" aria-hidden="true"><i class="bi bi-moon-stars"></i></span>
                        <div class="notify-card__body">
                            <strong>Besoin de soins ?</strong>
                            <span>Trouvez une pharmacie de garde ou un hôpital près de chez vous.</span>
                        </div>
                        <a href="{{ route('garde') }}" class="btn btn-medical btn-sm">Pharmacies de garde</a>
                    </div>
                @endif

                @if($articles->count())
                    <section class="related" aria-labelledby="art-title">
                        <div class="sec-head"><h2 id="art-title" class="sec-head__title">Pour aller plus loin</h2></div>
                        <div class="card-grid card-grid--3">
                            @foreach($articles as $a)
                                <x-post-card :post="$a" />
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>

            <aside class="layout__side" aria-label="Compléments">
                <div class="side-stack">
                    @if($voisines->count())
                        <div class="widget">
                            <div class="widget__head"><h2 class="widget__title"><i class="bi bi-journal-medical" aria-hidden="true"></i>Autres fiches</h2></div>
                            <div class="chips-wrap">
                                @foreach($voisines as $v)
                                    <a href="{{ route('maladies.show', $v->slug) }}" class="chip">{{ $v->nom }}</a>
                                @endforeach
                            </div>
                            <a href="{{ route('maladies.index') }}" class="widget__more mt-2">Toutes les maladies <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i></a>
                        </div>
                    @endif
                    <div class="widget">
                        <div class="widget__head"><h2 class="widget__title"><i class="bi bi-geo-alt" aria-hidden="true"></i>Besoin de soins ?</h2></div>
                        <p class="small text-muted">Pharmacies de garde, hôpitaux et numéros d'urgence près de chez vous.</p>
                        <a href="{{ route('annuaire') }}" class="btn btn-outline-medical btn-sm">Ouvrir l'annuaire santé</a>
                    </div>
                    <div class="sidebar__sticky"><x-ad-slot format="rectangle" /></div>
                </div>
            </aside>
        </div>
    </div>
</section>
@endsection
