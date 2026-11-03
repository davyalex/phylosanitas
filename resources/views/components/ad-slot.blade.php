{{--
    Emplacement publicitaire.
    Formats : leaderboard (bandeau), rectangle (sidebar), infeed (liste), inarticle (article).
    Ordre de priorité :
      1. publicité active gérée dans Admin > Publicités (image ou code)
      2. annonce AdSense si un identifiant d'emplacement est défini dans le .env
      3. encart « Espace partenaire » (si ADS_PLACEHOLDERS=true)
    Usage : <x-ad-slot format="leaderboard" />
--}}
@props(['format' => 'rectangle'])

@php
    $custom    = \App\Models\Publicite::pick($format);
    $client    = config('ads.client');
    $slot      = config("ads.slots.$format");
    $showHouse = config('ads.placeholders', true);
    $layoutKey = $format === 'infeed' ? config('ads.infeed_layout_key') : null;
    // In-article et In-feed (avec sa clé de mise en page) sont des annonces « fluid »
    $adFormat  = ($format === 'inarticle' || $layoutKey) ? 'fluid' : 'auto';

    // Impression comptée après l'envoi de la page (aucun ralentissement)
    if ($custom) {
        $adId = $custom->id;
        app()->terminating(function () use ($adId) {
            try { \Illuminate\Support\Facades\DB::table('publicites')->where('id', $adId)->increment('impressions'); } catch (\Throwable) {}
        });
    }
@endphp

@if($custom || ($client && $slot) || $showHouse)
    <aside {{ $attributes->merge(['class' => "ad ad--$format"]) }} aria-label="Publicité">
        <span class="ad__label">Publicité</span>

        @if($custom && $custom->type === 'image')
            <a href="{{ $custom->lien ? route('publicite.click', $custom->id) : '#' }}"
               class="ad__banner" target="_blank" rel="sponsored noopener">
                <img src="{{ $custom->image }}" alt="{{ $custom->titre }}" loading="lazy" decoding="async">
            </a>
        @elseif($custom && $custom->type === 'code')
            <div class="ad__frame ad__frame--code">{!! $custom->code !!}</div>
        @elseif($client && $slot)
            <div class="ad__frame">
                <ins class="adsbygoogle"
                     style="display:block{{ $format === 'inarticle' ? '; text-align:center;' : '' }}"
                     data-ad-client="{{ $client }}"
                     data-ad-slot="{{ $slot }}"
                     data-ad-format="{{ $adFormat }}"
                     @if($format === 'inarticle') data-ad-layout="in-article" @endif
                     @if($layoutKey) data-ad-layout-key="{{ $layoutKey }}" @endif
                     @unless($adFormat === 'fluid') data-full-width-responsive="true" @endunless></ins>
            </div>
            <script>(window.adsbygoogle = window.adsbygoogle || []).push({});</script>
        @else
            <a href="{{ route('contact') }}" class="ad__house">
                <span class="ad__house-icon" aria-hidden="true"><i class="bi bi-megaphone"></i></span>
                <span class="ad__house-text">
                    <strong>Espace partenaire</strong>
                    <span>Intéressé par un partenariat&nbsp;? Votre message auprès des lecteurs de {{ config('app.name') }}.</span>
                </span>
                <span class="ad__house-cta">Contactez-nous <i class="bi bi-arrow-right"></i></span>
            </a>
        @endif
    </aside>
@endif
