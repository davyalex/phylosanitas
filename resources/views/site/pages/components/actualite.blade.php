<div class="card card-medical p-3 mb-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="aside-title text-medical-blue fw-bold mb-0">
            <i class="bi bi-newspaper me-2"></i>
            Actualités
        </h3>
        <a href="/post/?category=actualites" class="btn btn-sm btn-medical">
            <i class="bi bi-arrow-right"></i>
        </a>
    </div>
    
    <div class="actualites-carousel">
        <div class="swiper actualitesSwiper">
            <div class="swiper-wrapper">
                @foreach ($actualite_externe as $item)
                    <div class="swiper-slide">
                        <a href="/post/detail?slug={{ $item['slug'] }}" class="text-decoration-none">
                            <div class="actualite-card position-relative overflow-hidden rounded">
                                @if($item->getFirstMediaUrl('image'))
                                    <img src="{{ $item->getFirstMediaUrl('image') }}" 
                                         class="w-100" 
                                         style="height: 180px; object-fit: cover;"
                                         alt="{{ $item['title'] }}">
                                @else
                                    <img src="{{ asset('assets_site/img/medc.jpg') }}" 
                                         class="w-100" 
                                         style="height: 180px; object-fit: cover;"
                                         alt="{{ $item['title'] }}">
                                @endif
                                
                                <!-- Overlay gradient -->
                                <div class="actualite-overlay position-absolute bottom-0 w-100 p-3">
                                    <span class="badge badge-medical mb-2">
                                        <i class="bi bi-newspaper"></i> Actualité
                                    </span>
                                    <h6 class="text-white mb-2 fw-bold">
                                        {{ Str::limit($item['title'], 60, '...') }}
                                    </h6>
                                    <small class="text-white">
                                        <i class="bi bi-calendar3"></i>
                                        {{ \Carbon\Carbon::parse($item['created_at'])->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
            
            <!-- Navigation -->
            @if(count($actualite_externe) > 1)
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-pagination"></div>
            @endif
        </div>
    </div>
</div>

<style>
    .actualites-carousel {
        position: relative;
        min-height: 180px;
    }
    
    .actualitesSwiper {
        width: 100%;
        height: 100%;
    }
    
    .actualite-card {
        transition: var(--transition-normal);
        box-shadow: var(--shadow-sm);
        height: 180px;
        display: block;
    }
    
    .actualite-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
    }
    
    .actualite-overlay {
        background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
    }
    
    .actualitesSwiper .swiper-button-next,
    .actualitesSwiper .swiper-button-prev {
        width: 32px;
        height: 32px;
        background: var(--medical-blue);
        border-radius: 50%;
        color: white;
    }
    
    .actualitesSwiper .swiper-button-next:after,
    .actualitesSwiper .swiper-button-prev:after {
        font-size: 14px;
        font-weight: bold;
    }
    
    .actualitesSwiper .swiper-button-next:hover,
    .actualitesSwiper .swiper-button-prev:hover {
        background: var(--medical-blue-dark);
    }
    
    .actualitesSwiper .swiper-pagination-bullet {
        background: var(--medical-blue);
        opacity: 0.5;
    }
    
    .actualitesSwiper .swiper-pagination-bullet-active {
        opacity: 1;
        background: var(--medical-blue);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Attendre un peu pour s'assurer que tout est chargé
        setTimeout(function() {
            if (typeof Swiper !== 'undefined') {
                const actualiteSwiper = new Swiper('.actualitesSwiper', {
                    slidesPerView: 1,
                    spaceBetween: 0,
                    loop: true,
                    autoplay: {
                        delay: 5000,
                        disableOnInteraction: false,
                    },
                    pagination: {
                        el: '.actualitesSwiper .swiper-pagination',
                        clickable: true,
                    },
                    navigation: {
                        nextEl: '.actualitesSwiper .swiper-button-next',
                        prevEl: '.actualitesSwiper .swiper-button-prev',
                    },
                    effect: 'fade',
                    fadeEffect: {
                        crossFade: true
                    },
                });
                console.log('Actualités Swiper initialisé:', actualiteSwiper);
            } else {
                console.error('Swiper non disponible');
            }
        }, 100);
    });
</script>
