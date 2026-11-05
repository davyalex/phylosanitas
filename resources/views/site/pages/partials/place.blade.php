{{-- Fiche d'un établissement de l'annuaire. Variable : $e (Etablissement) --}}
@php
    $t     = \App\Models\Etablissement::TYPES[$e->type];
    $carte = $e->carteUrl();
    $lieu  = collect([$e->quartier, $e->ville])->filter()->implode(', ');
@endphp
<article class="place">
    <span class="place__type"><i class="bi bi-{{ $t['icon'] }}" aria-hidden="true"></i>{{ $t['label'] }}@if($e->estDeGarde())<span class="place__garde"><i class="bi bi-moon-stars-fill" aria-hidden="true"></i>De garde</span>@endif</span>
    <h3 class="place__name">{{ $e->nom }}</h3>
    <ul class="place__info">
        @if($lieu || $e->adresse)
            <li><i class="bi bi-geo-alt" aria-hidden="true"></i><span>{{ collect([$e->adresse, $lieu])->filter()->implode(' — ') }}</span></li>
        @endif
        @if($e->horaires)<li><i class="bi bi-clock" aria-hidden="true"></i><span>{{ $e->horaires }}</span></li>@endif
        @if($e->notes)<li><i class="bi bi-info-circle" aria-hidden="true"></i><span>{{ $e->notes }}</span></li>@endif
        @if($e->telephone_2)<li><i class="bi bi-telephone" aria-hidden="true"></i><a href="{{ \App\Models\Etablissement::telHref($e->telephone_2) }}">{{ $e->telephone_2 }}</a></li>@endif
    </ul>
    @if($e->telephone || $carte)
        <div class="place__actions">
            @if($e->telephone)
                <a href="{{ \App\Models\Etablissement::telHref($e->telephone) }}" class="btn btn-medical btn-sm"><i class="bi bi-telephone-fill" aria-hidden="true"></i>{{ $e->telephone }}</a>
            @endif
            @if($carte)
                <a href="{{ $carte }}" target="_blank" rel="noopener" class="btn btn-outline-medical btn-sm"><i class="bi bi-map" aria-hidden="true"></i>Itinéraire</a>
            @endif
        </div>
    @endif
</article>
