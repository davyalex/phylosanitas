{{--
    Gabarit commun des outils santé.
    Sections : tool_form (champs), tool_result (résultat), tool_explain (texte explicatif)
--}}
@extends('site.layout')
@section('title', $outil['titre'])
@section('description', $outil['meta'])

@push('jsonld')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type'    => 'WebApplication',
    'name'     => $outil['titre'],
    'description' => $outil['meta'],
    'applicationCategory' => 'HealthApplication',
    'operatingSystem' => 'Tous',
    'inLanguage' => 'fr-FR',
    'isAccessibleForFree' => true,
    'offers'   => ['@type' => 'Offer', 'price' => 0, 'priceCurrency' => 'XOF'],
    'url'      => route('outils.show', $outil['slug']),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@section('content')
<x-svc-hero :icon="$outil['icon']" :titre="$outil['titre']" :parent="['Outils santé' => route('outils.index')]">
    {{ $outil['texte'] }}
</x-svc-hero>

<section class="svc-body">
    <div class="container-xl">
        <div class="tool">
            <form class="tool__form js-tool" novalidate>
                <h2>Vos informations</h2>
                @yield('tool_form')
                <button type="submit" class="btn btn-medical btn-block"><i class="bi bi-calculator" aria-hidden="true"></i> Calculer</button>
                <p class="field__hint mt-2 text-center"><i class="bi bi-lock" aria-hidden="true"></i> Calcul fait sur votre appareil : rien n'est envoyé ni enregistré.</p>
            </form>

            <div class="tool__result" aria-live="polite">
                <div class="tool__placeholder js-tool-empty">
                    <i class="bi bi-{{ $outil['icon'] }}" aria-hidden="true"></i>
                    Remplissez le formulaire puis cliquez sur « Calculer ».
                </div>
                <div class="js-tool-out" hidden>
                    @yield('tool_result')
                </div>
            </div>
        </div>

        <div class="tool-explain">
            @yield('tool_explain')
        </div>

        <aside class="disclaimer" aria-label="Avertissement">
            <i class="bi bi-info-circle" aria-hidden="true"></i>
            <p>Ce résultat est un repère général et ne remplace pas l'avis d'un professionnel de santé.@if(config('sante.modules.questions')) Une question ? <a href="{{ route('questions.index') }}">Le médecin vous répond</a>.@endif</p>
        </aside>

        <div class="mini-head"><h2>Autres outils</h2></div>
        <div class="svc-grid">
            @foreach($autres as $slug => $o)
                <article class="svc">
                    <span class="svc__icon" aria-hidden="true"><i class="bi bi-{{ $o['icon'] }}"></i></span>
                    <div>
                        <h3 class="svc__title"><a href="{{ route('outils.show', $slug) }}" class="svc__link">{{ $o['titre'] }}</a></h3>
                        <p class="svc__text">{{ $o['texte'] }}</p>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

<script>
    /* Utilitaire commun : affiche le bloc résultat */
    window.psShowResult = function () {
        document.querySelector('.js-tool-empty').hidden = true;
        var out = document.querySelector('.js-tool-out');
        out.hidden = false;
        if (window.matchMedia('(max-width: 991.98px)').matches) out.closest('.tool__result').scrollIntoView({ behavior: 'smooth', block: 'start' });
    };
    window.psFmt = function (n, d) { return Number(n).toLocaleString('fr-FR', { minimumFractionDigits: d || 0, maximumFractionDigits: d || 0 }); };
    window.psDate = function (dt) { return dt.toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }); };
</script>
@endsection
