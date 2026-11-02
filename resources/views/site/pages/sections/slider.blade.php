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
            <div class="hero__copy">
                <h1 class="hero__title">{{ $site['slogan'] }}</h1>
                <p class="hero__text">Articles médicaux, conseils de prévention et actualités santé, rédigés par un médecin.</p>

                <form action="{{ route('search') }}" method="GET" class="search-field search-field--hero" role="search">
                    <i class="bi bi-search" aria-hidden="true"></i>
                    <label for="hero-search" class="visually-hidden">Rechercher un sujet santé</label>
                    <input id="hero-search" type="search" name="query" placeholder="Diabète, nutrition, vaccination…" minlength="2" required>
                    <button type="submit" class="btn btn-medical">Rechercher</button>
                </form>

                <a href="{{ route('post.list') }}" class="hero__more">Parcourir tous les articles <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
            </div>

            {{-- Illustration santé (desktop uniquement, décorative) --}}
            <div class="hero__art" aria-hidden="true">
                <svg class="hero__svg" viewBox="0 0 480 420" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <radialGradient id="heroGlow" cx="50%" cy="45%" r="55%">
                            <stop offset="0" stop-color="#fff" stop-opacity=".16"/>
                            <stop offset="1" stop-color="#fff" stop-opacity="0"/>
                        </radialGradient>
                        <linearGradient id="heroHeart" x1="160" y1="130" x2="320" y2="300" gradientUnits="userSpaceOnUse">
                            <stop offset="0" stop-color="#fff" stop-opacity=".22"/>
                            <stop offset="1" stop-color="#fff" stop-opacity=".05"/>
                        </linearGradient>
                    </defs>

                    {{-- Halo + anneaux --}}
                    <circle cx="240" cy="210" r="200" fill="url(#heroGlow)"/>
                    <circle cx="240" cy="210" r="186" stroke="#fff" stroke-opacity=".12" stroke-dasharray="2 10" stroke-linecap="round" stroke-width="2"/>
                    <circle cx="240" cy="210" r="140" stroke="#fff" stroke-opacity=".14"/>

                    {{-- Cœur --}}
                    <path d="M240 296C168 246 146 204 158 166c12-38 58-50 82-12 24-38 70-26 82 12 12 38-10 80-82 130Z"
                          fill="url(#heroHeart)" stroke="#fff" stroke-opacity=".85" stroke-width="3" stroke-linejoin="round"/>

                    {{-- Tracé ECG --}}
                    <path class="hero__ecg" d="M96 214h96l12-26 16 58 18-84 16 70 10-18h120"
                          stroke="#3fd6a0" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle class="hero__ecg-dot" cx="384" cy="214" r="6" fill="#3fd6a0"/>

                    {{-- Stéthoscope --}}
                    <g stroke="#fff" stroke-opacity=".7" stroke-width="3" stroke-linecap="round" fill="none">
                        <path d="M318 312c0 34 24 52 50 52s46-20 46-50v-26"/>
                        <circle cx="414" cy="276" r="12" fill="#0a2c52" stroke-opacity=".85"/>
                        <circle cx="414" cy="276" r="4" fill="#fff" fill-opacity=".85" stroke="none"/>
                    </g>

                    {{-- Croix médicales --}}
                    <g fill="#fff">
                        <path opacity=".5" d="M70 96h8v-8h6v8h8v6h-8v8h-6v-8h-8z"/>
                        <path opacity=".35" d="M402 70h6v-6h4v6h6v4h-6v6h-4v-6h-6z"/>
                        <path opacity=".3" d="M120 350h6v-6h4v6h6v4h-6v6h-4v-6h-6z"/>
                    </g>
                    <path fill="#3fd6a0" opacity=".8" d="M436 160h6v-6h4v6h6v4h-6v6h-4v-6h-6z"/>
                    <circle cx="96" cy="250" r="4" fill="#3fd6a0" opacity=".7"/>
                    <circle cx="370" cy="110" r="3" fill="#fff" opacity=".5"/>
                </svg>

                <span class="hero__chip hero__chip--a"><i class="bi bi-capsule"></i></span>
                <span class="hero__chip hero__chip--b hero__chip--green"><i class="bi bi-shield-plus"></i></span>
                <span class="hero__chip hero__chip--c"><i class="bi bi-droplet-half"></i></span>

                <span class="hero__badge">
                    <span class="hero__badge-icon"><i class="bi bi-patch-check-fill"></i></span>
                    <span>
                        <strong>Rédigé par un médecin</strong>
                        <small>Information &amp; prévention</small>
                    </span>
                </span>
            </div>
        </div>
    </section>
@endif
