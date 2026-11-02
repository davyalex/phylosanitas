{{--
    Prévention : 4 bons réflexes illustrés, chacun mène à la recherche d'articles.
    Paramètre optionnel : $compact (bool) pour l'afficher dans une colonne (pages liste / recherche).
--}}
@php
    $compact = $compact ?? false;
    $tips = [
        ['art' => 'food',      'title' => 'Bien manger',         'text' => 'Fruits, légumes, moins de sel et de sucre : la base d\'une bonne santé.', 'q' => 'nutrition'],
        ['art' => 'move',      'title' => 'Bouger chaque jour',  'text' => '30 minutes de marche par jour protègent le cœur et le moral.',           'q' => 'sport'],
        ['art' => 'vaccine',   'title' => 'Se faire vacciner',   'text' => 'Les vaccins à jour protègent toute la famille, à tout âge.',              'q' => 'vaccination'],
        ['art' => 'screening', 'title' => 'Se faire dépister',   'text' => 'Tension, glycémie, cancers : un dépistage précoce change tout.',          'q' => 'dépistage'],
    ];
@endphp

<section class="prevention {{ $compact ? 'prevention--compact' : 'section' }}" aria-labelledby="prev-title">
    <div class="{{ $compact ? '' : 'container-xl' }}">
        <div class="prevention__head">
            <span class="eyebrow"><i class="bi bi-shield-check" aria-hidden="true"></i>Prévention</span>
            <h2 id="prev-title" class="prevention__title">Les bons réflexes au quotidien</h2>
            @unless($compact)
                <p class="prevention__lead">Quatre gestes simples, recommandés par votre médecin, pour rester en bonne santé.</p>
            @endunless
        </div>

        <div class="prevention__grid">
            @foreach($tips as $tip)
                <article class="ptile">
                    <x-health-art :name="$tip['art']" class="ptile__art" />
                    <div class="ptile__body">
                        <h3 class="ptile__title">
                            <a href="{{ route('search', ['query' => $tip['q']]) }}" class="ptile__link">{{ $tip['title'] }}</a>
                        </h3>
                        <p class="ptile__text">{{ $tip['text'] }}</p>
                        <span class="ptile__more" aria-hidden="true">Lire les conseils <i class="bi bi-arrow-right"></i></span>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
