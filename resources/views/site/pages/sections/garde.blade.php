{{-- Accueil : bandeau « Pharmacies de garde » placé tout en haut (consulté presque chaque jour) --}}
@php
    $gj  = $gardes_jour ?? \App\Models\Etablissement::gardesDuJour();
    $fin = $gj->min('garde_fin');
    $apercu = $gj->take(3);
@endphp
<section class="garde-band" aria-labelledby="garde-band-title">
    <div class="container-xl garde-band__inner">
        <div class="garde-band__head">
            <span class="garde-band__icon" aria-hidden="true"><i class="bi bi-moon-stars-fill"></i></span>
            <div>
                <h2 class="garde-band__title" id="garde-band-title">Pharmacies de garde</h2>
                <p class="garde-band__sub">
                    @if($gj->count())
                        {{ $gj->count() }} pharmacie{{ $gj->count() > 1 ? 's' : '' }} de garde jusqu'au {{ $fin->translatedFormat('l j F') }}
                    @else
                        La liste de la semaine est mise à jour chaque lundi
                    @endif
                </p>
            </div>
        </div>

        @if($apercu->count())
            <ul class="garde-band__list" aria-label="Aperçu des pharmacies de garde">
                @foreach($apercu as $e)
                    <li>
                        <strong>{{ $e->nom }}</strong>
                        <span>{{ collect([$e->quartier, $e->ville])->filter()->implode(', ') }}</span>
                        @if($e->telephone)
                            <a href="{{ \App\Models\Etablissement::telHref($e->telephone) }}" class="garde-band__call" aria-label="Appeler {{ $e->nom }}"><i class="bi bi-telephone-fill" aria-hidden="true"></i></a>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif

        <a href="{{ route('garde') }}" class="btn btn-hero garde-band__cta">
            {{ $gj->count() ? 'Voir toute la liste' : 'Voir les pharmacies' }} <i class="bi bi-arrow-right" aria-hidden="true"></i>
        </a>
    </div>
</section>
