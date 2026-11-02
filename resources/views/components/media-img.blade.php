{{--
    Image d'un modèle Spatie Media (collection "image").
    - Utilise la miniature WebP (thumb 800px / large 1280px) si elle existe,
      sinon l'original, sinon l'image par défaut.
    - Dimensions réservées, chargement paresseux, repli en cas d'erreur.
    Usage : <x-media-img :model="$post" size="thumb" sizes="(min-width: 992px) 280px, 100vw" />
--}}
@props([
    'model'  => null,
    'size'   => 'thumb',      // thumb | large
    'alt'    => '',
    'eager'  => false,
    'sizes'  => '100vw',
    'width'  => 800,
    'height' => 500,
])

@php
    $fallback = asset('assets_site/img/medc.jpg');
    $media    = $model?->getFirstMedia('image');
    $src      = $fallback;
    $srcset   = null;

    if ($media) {
        $hasThumb = $media->hasGeneratedConversion('thumb');
        $hasLarge = $media->hasGeneratedConversion('large');

        if ($size === 'large') {
            $src = $hasLarge ? $media->getUrl('large') : $media->getUrl();
        } else {
            $src = $hasThumb ? $media->getUrl('thumb') : $media->getUrl();
        }

        if ($hasThumb && $hasLarge) {
            $srcset = $media->getUrl('thumb') . ' 800w, ' . $media->getUrl('large') . ' 1280w';
        }
    }
@endphp

<img src="{{ $src }}"
     @if($srcset) srcset="{{ $srcset }}" sizes="{{ $sizes }}" @endif
     width="{{ $width }}" height="{{ $height }}"
     alt="{{ $alt }}"
     loading="{{ $eager ? 'eager' : 'lazy' }}"
     decoding="async"
     @if($eager) fetchpriority="high" @endif
     onerror="this.onerror=null;this.removeAttribute('srcset');this.src='{{ $fallback }}'"
     {{ $attributes }}>
