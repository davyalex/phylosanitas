{{-- Barre latérale : pharmacies de garde du jour (4 premières) --}}
@php $gj = $gardes_jour ?? \App\Models\Etablissement::gardesDuJour(); @endphp
@if($gj->count() && !request()->routeIs('garde'))
    <div class="widget widget--garde">
        <div class="widget__head">
            <h2 class="widget__title"><i class="bi bi-moon-stars-fill" aria-hidden="true"></i>De garde cette semaine</h2>
        </div>
        <ul class="garde-mini">
            @foreach($gj->take(4) as $e)
                <li>
                    <span class="garde-mini__info">
                        <strong>{{ $e->nom }}</strong>
                        <small>{{ collect([$e->quartier, $e->ville])->filter()->implode(', ') }}</small>
                    </span>
                    @if($e->telephone)
                        <a href="{{ \App\Models\Etablissement::telHref($e->telephone) }}" class="garde-mini__call" aria-label="Appeler {{ $e->nom }}"><i class="bi bi-telephone-fill" aria-hidden="true"></i></a>
                    @endif
                </li>
            @endforeach
        </ul>
        <a href="{{ route('garde') }}" class="btn btn-medical btn-sm btn-block mt-3">Les {{ $gj->count() }} pharmacies de garde <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
    </div>
@endif
