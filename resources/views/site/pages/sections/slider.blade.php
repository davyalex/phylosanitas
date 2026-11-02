{{-- Hero — carrousel géré depuis admin > Carrousel Hero, sinon bannière par défaut --}}
@if($slide->count())
    <section class="hero hero--slider" aria-label="À découvrir">
        <h1 class="visually-hidden">{{ config('app.name') }} — {{ $site['slogan'] }}</h1>
        <div class="swiper heroSwiper">
            <div class="swiper-wrapper">
                @foreach($slide as $item)
                    @php
                        $href  = $item->lien ?: route('post.list');
                        $label = $item->texte_bouton ?: "Lire l'article";
                        $img   = $item->getFirstMediaUrl('image') ?: asset('assets_site/img/medc.jpg');
                    @endphp
                    <div class="swiper-slide hero__slide">
                        <img class="hero__img" src="{{ $img }}" alt="" width="1600" height="700"
                             loading="{{ $loop->first ? 'eager' : 'lazy' }}" decoding="async"
                             @if($loop->first) fetchpriority="high" @endif
                             onerror="this.onerror=null;this.src='{{ asset('assets_site/img/medc.jpg') }}'">
                        <div class="hero__shade" aria-hidden="true"></div>
                        <div class="container-xl hero__content">
                            <h2 class="hero__title">{{ $item->title }}</h2>
                            @if($item->sous_titre)
                                <p class="hero__text">{{ $item->sous_titre }}</p>
                            @endif
                            <a href="{{ $href }}" class="btn btn-hero">
                                {{ $label }} <i class="bi bi-arrow-right" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            @if($slide->count() > 1)
                <div class="hero__controls container-xl">
                    <div class="swiper-pagination hero__pagination"></div>
                    <div class="hero__arrows">
                        <button type="button" class="hero__arrow hero-prev" aria-label="Diapositive précédente"><i class="bi bi-chevron-left" aria-hidden="true"></i></button>
                        <button type="button" class="hero__arrow hero-next" aria-label="Diapositive suivante"><i class="bi bi-chevron-right" aria-hidden="true"></i></button>
                    </div>
                </div>
            @endif
        </div>
    </section>
@else
    <section class="hero hero--intro">
        <div class="container-xl hero__intro">
            <h1 class="hero__title">{{ $site['slogan'] }}</h1>
            <p class="hero__text">Articles médicaux, actualités santé et conseils bien-être rédigés par des professionnels de santé.</p>

            <form action="{{ route('search') }}" method="GET" class="search-field search-field--hero" role="search">
                <i class="bi bi-search" aria-hidden="true"></i>
                <label for="hero-search" class="visually-hidden">Rechercher un sujet santé</label>
                <input id="hero-search" type="search" name="query" placeholder="Diabète, nutrition, vaccination…" minlength="2" required>
                <button type="submit" class="btn btn-medical">Rechercher</button>
            </form>

            <a href="{{ route('post.list') }}" class="hero__more">Parcourir tous les articles <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
        </div>
    </section>
@endif
