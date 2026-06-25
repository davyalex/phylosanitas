{{-- Hero Slider — géré depuis admin > Carrousel Hero --}}
<section id="hero-slider" class="hero-slider">

    @if($slide->count())
        <div class="swiper heroSwiper">
            <div class="swiper-wrapper">
                @foreach($slide as $item)
                    <div class="swiper-slide">
                        <div class="hero-slide"
                             style="background-image: url('{{ $item->getFirstMediaUrl('image') ?: asset('assets_site/img/medc.jpg') }}');">
                            <div class="hero-slide-overlay"></div>
                            <div class="container-xl hero-slide-content text-center">
                                <span class="hero-badge" data-aos="fade-down" data-aos-delay="100">
                                    <i class="bi bi-newspaper me-1"></i>Actualité santé
                                </span>
                                <h1 class="hero-title hero-title--large" data-aos="zoom-in" data-aos-delay="200">
                                    {{ Str::title($item->title) }}
                                </h1>
                                @if($item->sous_titre)
                                    <p class="hero-excerpt hero-excerpt--center" data-aos="fade-up" data-aos-delay="300">
                                        {{ $item->sous_titre }}
                                    </p>
                                @endif
                                <div data-aos="fade-up" data-aos-delay="400">
                                    @php
                                        $href  = $item->lien ?: route('post.list');
                                        $label = $item->texte_bouton ?: "Lire l'article";
                                    @endphp
                                    <a href="{{ $href }}" class="btn hero-btn-primary btn-hero-lg">
                                        <i class="bi bi-book-half me-2"></i>{{ $label }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="hero-swiper-prev"><i class="bi bi-chevron-left"></i></div>
            <div class="hero-swiper-next"><i class="bi bi-chevron-right"></i></div>
            <div class="swiper-pagination hero-pagination"></div>
        </div>

    @else
        {{-- Bannière par défaut — texte centré en grand --}}
        <div class="hero-static hero-static--center">
            <div class="hero-slide-overlay"></div>
            <div class="container hero-static-content text-center">
                <span class="hero-badge hero-badge--pulse mb-4 d-inline-block">
                    <i class="bi bi-heart-pulse-fill me-2"></i>Blog Médical &amp; Santé
                </span>
                <h1 class="hero-static-title">
                    Votre Santé,<br>Notre Priorité
                </h1>
                <p class="hero-static-subtitle">
                    Articles médicaux · Actualités santé · Conseils bien-être<br>
                    rédigés par des professionnels de santé.
                </p>
                <div class="hero-static-actions">
                    <a href="{{ route('post.list') }}" class="btn hero-btn-primary btn-hero-lg me-3">
                        <i class="bi bi-collection-fill me-2"></i>Parcourir les articles
                    </a>
                    <a href="{{ route('contact') }}" class="btn hero-btn-secondary btn-hero-lg">
                        <i class="bi bi-envelope-fill me-2"></i>Nous contacter
                    </a>
                </div>
            </div>
        </div>
    @endif

</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Swiper !== 'undefined' && document.querySelector('.heroSwiper')) {
            new Swiper('.heroSwiper', {
                loop: true,
                autoplay: { delay: 5500, disableOnInteraction: false },
                speed: 900,
                effect: 'fade',
                fadeEffect: { crossFade: true },
                pagination: { el: '.hero-pagination', clickable: true },
                navigation: { nextEl: '.hero-swiper-next', prevEl: '.hero-swiper-prev' },
                a11y: { prevSlideMessage: 'Slide précédent', nextSlideMessage: 'Slide suivant' },
            });
        }
    });
</script>
