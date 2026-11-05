{{--
    Accueil, sous « Quand consulter ? » : deux cartes toujours remplies qui comblent la colonne face à la barre latérale.
    1re carte : les prochaines journées santé du calendrier.
    2e carte : outils santé.
--}}
@php
    // Journée en cours, puis les prochaines (on complète avec l'année suivante en fin d'année)
    $journees = collect();
    try {
        $g = \App\Models\JourneeSante::calendrierAnnee();
        $journees = $g['en_cours']->concat($g['a_venir']);
        if ($journees->count() < 3) {
            $suivante = \App\Models\JourneeSante::calendrierAnnee(now()->addYear()->startOfYear());
            $journees = $journees->concat($suivante['en_cours'])->concat($suivante['a_venir']);
        }
        $journees = $journees->take(3);
    } catch (\Throwable) {}

    $outils = config('sante.outils', []);
@endphp

<div class="reperes">
    @if($journees->count())
        <section class="reperes__card" aria-labelledby="rep-journees">
            <div class="reperes__head">
                <span class="reperes__icon" aria-hidden="true"><i class="bi bi-calendar-heart"></i></span>
                <div>
                    <h2 class="reperes__title" id="rep-journees">Journées santé</h2>
                    <p class="reperes__sub">Les prochaines dates à retenir</p>
                </div>
            </div>
            <ul class="rep-days">
                @foreach($journees as $j)
                    <li style="--c: {{ $j->couleur }}">
                        <span class="rep-days__date" aria-hidden="true">
                            @if($j->estMensuelle())<i class="bi bi-calendar-heart"></i>@else<strong>{{ $j->debut->day }}</strong>@endif
                            <small>{{ $j->debut->translatedFormat('M') }}</small>
                        </span>
                        <span class="rep-days__body">
                            <strong>{{ $j->titre }}</strong>
                            <small>
                                @if($j->debut->lte(now()) && $j->fin->gte(now()->startOfDay()))
                                    {{ $j->estMensuelle() ? 'Tout le mois' : "C'est aujourd'hui" }}
                                @else
                                    {{ $j->date_libelle }}
                                @endif
                            </small>
                        </span>
                    </li>
                @endforeach
            </ul>
            <a href="{{ route('calendrier') }}" class="reperes__more">Tout le calendrier santé <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
        </section>
    @endif

    <section class="reperes__card" aria-labelledby="rep-outils">
        <div class="reperes__head">
            <span class="reperes__icon reperes__icon--green" aria-hidden="true"><i class="bi bi-calculator"></i></span>
            <div>
                <h2 class="reperes__title" id="rep-outils">Outils santé</h2>
                <p class="reperes__sub">Gratuits, rien n'est enregistré</p>
            </div>
        </div>
        <ul class="quick-list">
            @foreach($outils as $slug => $o)
                <li>
                    <a href="{{ route('outils.show', $slug) }}">
                        <span class="quick-list__icon" aria-hidden="true"><i class="bi bi-{{ $o['icon'] }}"></i></span>
                        <span>{{ $o['titre'] }}</span>
                        <i class="bi bi-chevron-right quick-list__arrow" aria-hidden="true"></i>
                    </a>
                </li>
            @endforeach
        </ul>
        <a href="{{ route('outils.index') }}" class="reperes__more">Tous les outils santé <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
    </section>
</div>
