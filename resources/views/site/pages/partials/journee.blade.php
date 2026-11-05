{{-- Une journée du calendrier santé. Variable : $j (JourneeSante avec debut, fin, etat, dans|retour) --}}
@php
    $href = $j->lien ?: route('search', ['query' => (string) Str::of($j->titre)->replaceMatches('/^(journée mondiale (de lutte )?(contre |de la |du |de l\'|des |de )?)/iu', '')->trim()]);
    $quand = match ($j->etat) {
        'en_cours' => $j->estMensuelle() ? 'Tout le mois' : "C'est aujourd'hui",
        'a_venir'  => $j->dans === 1 ? 'Demain' : ($j->dans <= 31 ? 'Dans ' . $j->dans . ' jours' : ucfirst($j->debut->translatedFormat('l j F'))),
        default    => 'Prochaine édition : ' . ($j->estMensuelle() ? $j->retour->translatedFormat('F Y') : $j->retour->translatedFormat('j F Y')),
    };
@endphp
<li class="cal__item cal__item--{{ $j->etat }} {{ $j->etat === 'en_cours' ? 'is-now' : '' }}" style="--c: {{ $j->couleur }}">
    <div class="cal__date" aria-hidden="true">
        @if($j->estMensuelle())
            <span class="cal__day"><i class="bi bi-calendar-heart"></i></span>
        @else
            <span class="cal__day">{{ $j->debut->day }}</span>
        @endif
        <span class="cal__month">{{ $j->debut->translatedFormat('M') }}</span>
    </div>
    <div>
        <h3 class="cal__title"><a href="{{ $href }}">{{ $j->titre }}</a></h3>
        <span class="visually-hidden">{{ $j->date_libelle }}</span>
        @if($j->description && $j->etat !== 'passee')<p class="cal__text">{{ $j->description }}</p>@endif
        <span class="cal__when">{{ $quand }}</span>
    </div>
</li>
