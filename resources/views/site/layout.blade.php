<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-MTG6JQ5MNP"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());
        gtag('config', 'G-MTG6JQ5MNP');
    </script>

    <!-- Google Tag Manager -->
    <script>
        (function(w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({ 'gtm.start': new Date().getTime(), event: 'gtm.js' });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s),
                dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src = 'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-P2HGDT42');
    </script>

    @php
        $site      = $site ?? \App\Models\Setting::forViews();
        $siteName  = config('app.name');
        $yield     = fn($k, $d = '') => trim(html_entity_decode($__env->yieldContent($k, $d), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $pageTitle = $yield('title', 'Accueil');
        $fullTitle = $yield('meta_title') ?: ($pageTitle === 'Accueil' ? "$siteName — {$site['slogan']}" : "$pageTitle — $siteName");
        $metaDesc  = $yield('description') ?: ($site['description'] ?: "$siteName — Articles médicaux, actualités santé, sondages et conseils bien-être.");
        $canonical = $yield('canonical') ?: url()->current() . (request('page') > 1 ? '?page=' . (int) request('page') : '');
        $ogImage   = $yield('image') ?: $site['logo'];
    @endphp

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#004A99">
    <title>{{ $fullTitle }}</title>
    <meta name="description" content="{{ Str::limit($metaDesc, 160, '…') }}">
    <meta name="robots" content="@yield('robots', 'index, follow, max-image-preview:large')">
    <link rel="canonical" href="{{ $canonical }}">

    {{-- Open Graph / réseaux sociaux --}}
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:locale" content="fr_FR">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="{{ $fullTitle }}">
    <meta property="og:description" content="{{ Str::limit($metaDesc, 200, '…') }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $fullTitle }}">
    <meta name="twitter:description" content="{{ Str::limit($metaDesc, 200, '…') }}">
    <meta name="twitter:image" content="{{ $ogImage }}">
    @stack('meta')

    <meta name="google-adsense-account" content="ca-pub-6925205610540207">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Données structurées : site + organisation --}}
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@graph'   => [
            [
                '@type'     => 'WebSite',
                '@id'       => url('/') . '#website',
                'url'       => url('/'),
                'name'      => $siteName,
                'inLanguage'=> 'fr-FR',
                'publisher' => ['@id' => url('/') . '#organization'],
                'potentialAction' => [
                    '@type'       => 'SearchAction',
                    'target'      => route('search') . '?query={search_term_string}',
                    'query-input' => 'required name=search_term_string',
                ],
            ],
            [
                '@type' => 'Organization',
                '@id'   => url('/') . '#organization',
                'name'  => $siteName,
                'url'   => url('/'),
                'logo'  => $site['logo'],
                'slogan'=> $site['slogan'],
                'sameAs'=> array_values(array_column($site['socials'], 'url')),
                'address' => array_filter(['@type' => 'PostalAddress', 'streetAddress' => $site['adresse'], 'addressLocality' => $site['ville'], 'addressCountry' => $site['pays_code']]),
                'contactPoint' => array_merge(
                    array_map(fn($p) => array_filter(['@type' => 'ContactPoint', 'telephone' => $p['value'], 'contactType' => $p['label'] ?: 'customer service']), $site['phones']),
                    array_map(fn($e) => array_filter(['@type' => 'ContactPoint', 'email' => $e['value'], 'contactType' => $e['label'] ?: 'customer service']), $site['emails'])
                ),
            ],
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    @stack('jsonld')

    <!-- Favicons -->
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets_site/img/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets_site/img/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets_site/img/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ route('manifest') }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">

    <!-- Fonts : Source Serif 4 (titres) + Source Sans 3 (texte) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Source+Serif+4:opsz,wght@8..60,600;8..60,700&display=swap" rel="stylesheet">

    <!-- Vendor CSS -->
    <link href="{{ asset('assets_site/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('assets_site/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">

    <!-- Thème du site -->
    <link href="{{ asset('assets_site/css/site.css') }}?v={{ @filemtime(public_path('assets_site/css/site.css')) }}" rel="stylesheet">
    <link href="{{ asset('assets_site/css/site-motion.css') }}?v={{ @filemtime(public_path('assets_site/css/site-motion.css')) }}" rel="stylesheet">
    <link href="{{ asset('assets_site/css/site-services.css') }}?v={{ @filemtime(public_path('assets_site/css/site-services.css')) }}" rel="stylesheet">

    @stack('head')

    {{-- Google AdSense : script fourni par Google, identique sur toutes les pages (ne dépend pas du .env) --}}
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-6925205610540207"
     crossorigin="anonymous"></script>
</head>

@php
    $navCategories = $category->filter(fn($c) => !in_array(strtolower($c->slug), ['sondage', 'actualites']));
    $navServices   = config('sante.services', []);
    $serviceActif  = collect(array_keys($navServices))->first(fn($r) => request()->routeIs(Str::before($r, '.') . '*'));
@endphp

<body>
    <!-- Google Tag Manager (noscript) -->
    <noscript>
        <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-P2HGDT42" height="0" width="0" style="display:none;visibility:hidden"></iframe>
    </noscript>

    <a href="#main" class="skip-link">Aller au contenu</a>

    {{-- ======= Barre utilitaire (desktop) ======= --}}
    <div class="topbar">
        <div class="container-xl topbar__inner">
            <div class="topbar__info">
                @if($site['adresse'])<span><i class="bi bi-geo-alt" aria-hidden="true"></i>{{ $site['adresse'] }}</span>@endif
                @if($phone = $site['phones'][0] ?? null)<a href="{{ $phone['href'] }}"><i class="bi bi-telephone" aria-hidden="true"></i>{{ $phone['value'] }}</a>@endif
                @if($mail = $site['emails'][0] ?? null)<a href="{{ $mail['href'] }}" class="topbar__mail"><i class="bi bi-envelope" aria-hidden="true"></i>{{ $mail['value'] }}</a>@endif
                @if($site['show_visits'])
                    <span class="topbar__visits"><i class="bi bi-people" aria-hidden="true"></i>{{ number_format($site_visits['visites'] ?? 0, 0, ',', ' ') }} visites</span>
                @endif
            </div>
            <div class="topbar__social">
                @auth
                    <a href="{{ route('dashboard') }}" class="topbar__dash"><i class="bi bi-grid" aria-hidden="true"></i>Tableau de bord</a>
                @endauth
                @include('site.partials.socials')
            </div>
        </div>
    </div>

    {{-- ======= Header ======= --}}
    <header id="header" class="site-header">
        <div class="container-xl site-header__inner">

            <a href="{{ route('accueil') }}" class="brand" aria-label="{{ config('app.name') }} — accueil">
                <img src="{{ $site['logo'] }}" alt="{{ config('app.name') }}" width="180" height="48">
            </a>

            <nav class="nav" aria-label="Navigation principale">
                <ul class="nav__list">
                    <li><a href="{{ route('accueil') }}" class="nav__link {{ request()->routeIs('accueil') ? 'is-active' : '' }}" @if(request()->routeIs('accueil')) aria-current="page" @endif>Accueil</a></li>
                    @foreach ($navCategories as $item)
                        @php $active = request('category') === $item->slug; @endphp
                        <li>
                            <a class="nav__link {{ $active ? 'is-active' : '' }}" @if($active) aria-current="page" @endif
                               href="{{ route('post.list', ['category' => $item->slug]) }}">{{ $item->title }}</a>
                        </li>
                    @endforeach
                    <li class="nav__more">
                        <button type="button" class="nav__link nav__toggle {{ $serviceActif ? 'is-active' : '' }} js-nav-more" aria-expanded="false" aria-controls="nav-services">
                            Services santé <i class="bi bi-chevron-down" aria-hidden="true"></i>
                        </button>
                        <div id="nav-services" class="nav__panel" hidden>
                            <ul class="nav__grid">
                                @foreach($navServices as $route => $s)
                                    <li>
                                        <a href="{{ route($route) }}" class="nav__item {{ $serviceActif === $route ? 'is-active' : '' }}" @if($serviceActif === $route) aria-current="page" @endif>
                                            <span class="nav__item-icon" aria-hidden="true"><i class="bi bi-{{ $s['icon'] }}"></i></span>
                                            <span><strong>{{ $s['label'] }}</strong><small>{{ $s['texte'] }}</small></span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </li>
                    <li><a href="{{ route('contact') }}" class="nav__link {{ request()->routeIs('contact') ? 'is-active' : '' }}" @if(request()->routeIs('contact')) aria-current="page" @endif>Contact</a></li>
                </ul>
            </nav>

            <div class="site-header__actions">
                {{-- Accès direct aux pharmacies de garde (page la plus consultée) --}}
                <a href="{{ route('garde') }}" class="garde-btn {{ request()->routeIs('garde') ? 'is-active' : '' }}" title="Pharmacies de garde cette semaine" @if(request()->routeIs('garde')) aria-current="page" @endif>
                    <i class="bi bi-moon-stars-fill" aria-hidden="true"></i>
                    <span class="garde-btn__long">Pharmacies de garde</span>
                    <span class="garde-btn__short" aria-hidden="true">De garde</span>
                    @if(($gardes_jour ?? collect())->count())<span class="garde-btn__count" aria-label="{{ $gardes_jour->count() }} pharmacies">{{ $gardes_jour->count() }}</span>@endif
                </a>
                <button type="button" class="icon-btn js-search-toggle" aria-expanded="false" aria-controls="site-search" aria-label="Rechercher">
                    <i class="bi bi-search" aria-hidden="true"></i>
                </button>
                <button type="button" class="icon-btn icon-btn--menu js-drawer-open" aria-expanded="false" aria-controls="nav-drawer" aria-label="Ouvrir le menu">
                    <i class="bi bi-list" aria-hidden="true"></i>
                </button>
            </div>
        </div>

        {{-- Recherche --}}
        <div id="site-search" class="site-search" hidden>
            <div class="container-xl">
                <form action="{{ route('search') }}" method="GET" class="search-field" role="search">
                    <i class="bi bi-search" aria-hidden="true"></i>
                    <label for="site-search-input" class="visually-hidden">Rechercher un article</label>
                    <input id="site-search-input" type="search" name="query" placeholder="Rechercher un article : diabète, nutrition, vaccination…" minlength="2" required autocomplete="off">
                    <button type="submit" class="btn btn-medical">Rechercher</button>
                    <button type="button" class="icon-btn js-search-close" aria-label="Fermer la recherche"><i class="bi bi-x-lg" aria-hidden="true"></i></button>
                </form>
            </div>
        </div>
    </header>

    {{-- ======= Infos flash (gérées dans Admin > Infos flash) ======= --}}
    @if(($flash_infos ?? collect())->count())
        @php
            $topLevel = $flash_infos->first()->niveau;
            $moving   = $flash_infos->count() > 1 || Str::length($flash_infos->first()->message) > 70;
            $duration = max(20, (int) ceil($flash_infos->sum(fn($i) => Str::length($i->message)) / 6));
        @endphp
        <div class="flash flash--{{ $topLevel }}" role="region" aria-label="Infos flash">
            <div class="container-xl flash__inner">
                <span class="flash__label">
                    <i class="bi bi-{{ $topLevel === 'urgent' ? 'exclamation-octagon-fill' : 'lightning-charge-fill' }}" aria-hidden="true"></i>
                    {{ $topLevel === 'urgent' ? 'Urgent' : 'Infos flash' }}
                </span>
                <div class="flash__viewport">
                    <ul class="flash__track {{ $moving ? 'is-moving' : '' }}" style="--flash-duration: {{ $duration }}s">
                        @foreach([0, 1] as $pass)
                            @foreach($flash_infos as $info)
                                <li class="flash__item flash__item--{{ $info->niveau }}" @if($pass) aria-hidden="true" @endif>
                                    @if($info->lien)
                                        <a href="{{ $info->lien }}" @if($pass) tabindex="-1" @endif @unless(Str::startsWith($info->lien, url('/'))) target="_blank" rel="noopener" @endunless>{{ $info->message }}</a>
                                    @else
                                        <span>{{ $info->message }}</span>
                                    @endif
                                </li>
                            @endforeach
                        @endforeach
                    </ul>
                </div>
                @if($moving)
                    <button type="button" class="flash__pause js-flash-pause" aria-label="Mettre en pause le défilement" aria-pressed="false">
                        <i class="bi bi-pause-fill" aria-hidden="true"></i>
                    </button>
                @endif
            </div>
        </div>
    @endif

    {{-- ======= Journée / campagne de santé (Admin > Journées santé) ======= --}}
    @if($journee_sante ?? null)
        @php
            $js     = $journee_sante;
            $jsHref = $js->lien ?: route('search', ['query' => Str::of($js->titre)->replaceMatches('/^(journée mondiale (de lutte )?(contre |de la |du |de l\'|des |de )?)/iu', '')->trim()]);
            $jsWhen = match ($js->etat) {
                'jour'  => "Aujourd'hui",
                'mois'  => 'Ce mois-ci',
                default => $js->dans === 1 ? 'Demain' : 'Dans ' . $js->dans . ' jours',
            };
        @endphp
        <div class="hday js-hday" data-id="{{ $js->id }}-{{ $js->date }}" style="--hday: {{ $js->couleur }}" role="region" aria-label="Journée de santé">
            <div class="container-xl hday__inner">
                <span class="hday__when"><i class="bi bi-calendar-heart" aria-hidden="true"></i>{{ $jsWhen }}</span>
                <a href="{{ $jsHref }}" class="hday__link">
                    <strong>{{ $js->titre }}</strong>
                    @if($js->texte)<span class="hday__text">{{ $js->texte }}</span>@endif
                </a>
                <button type="button" class="hday__close js-hday-close" aria-label="Masquer ce bandeau"><i class="bi bi-x-lg" aria-hidden="true"></i></button>
            </div>
        </div>
    @endif

    {{-- ======= Menu mobile (tiroir) ======= --}}
    <div class="drawer-backdrop js-drawer-close" hidden></div>
    <aside id="nav-drawer" class="drawer" aria-label="Menu" aria-hidden="true">
        <div class="drawer__head">
            <img src="{{ $site['logo'] }}" alt="{{ config('app.name') }}" width="150" height="40">
            <button type="button" class="icon-btn js-drawer-close" aria-label="Fermer le menu"><i class="bi bi-x-lg" aria-hidden="true"></i></button>
        </div>

        <form action="{{ route('search') }}" method="GET" class="search-field search-field--compact" role="search">
            <i class="bi bi-search" aria-hidden="true"></i>
            <label for="drawer-search-input" class="visually-hidden">Rechercher un article</label>
            <input id="drawer-search-input" type="search" name="query" placeholder="Rechercher un article…" minlength="2" required>
        </form>

        <nav aria-label="Navigation mobile">
            <ul class="drawer__list">
                <li><a href="{{ route('accueil') }}" class="{{ request()->routeIs('accueil') ? 'is-active' : '' }}"><i class="bi bi-house-door" aria-hidden="true"></i>Accueil</a></li>
                @foreach ($navCategories as $item)
                    <li>
                        <a href="{{ route('post.list', ['category' => $item->slug]) }}" class="{{ request('category') === $item->slug ? 'is-active' : '' }}">
                            <x-cat-icon :cat="$item" />{{ $item->title }}
                            <span class="drawer__count">{{ $item->posts_count ?? '' }}</span>
                        </a>
                    </li>
                @endforeach
                <li><a href="{{ route('post.list', ['category' => 'sondage']) }}"><i class="bi bi-bar-chart-line" aria-hidden="true"></i>Sondages</a></li>
                <li class="drawer__label" aria-hidden="true">Services santé</li>
                @foreach($navServices as $route => $s)
                    <li><a href="{{ route($route) }}" class="{{ $serviceActif === $route ? 'is-active' : '' }}"><i class="bi bi-{{ $s['icon'] }}" aria-hidden="true"></i>{{ $s['label'] }}</a></li>
                @endforeach
                <li class="drawer__label" aria-hidden="true">Nous joindre</li>
                <li><a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'is-active' : '' }}"><i class="bi bi-envelope" aria-hidden="true"></i>Contact</a></li>
                @auth
                    <li><a href="{{ route('dashboard') }}"><i class="bi bi-grid" aria-hidden="true"></i>Tableau de bord</a></li>
                @endauth
            </ul>
        </nav>

        <div class="drawer__contact">
            @foreach($site['phones'] as $p)
                <a href="{{ $p['href'] }}"><i class="bi bi-telephone" aria-hidden="true"></i>{{ $p['value'] }}@if($p['label'])<small>{{ $p['label'] }}</small>@endif</a>
            @endforeach
            @if($site['show_visits'])
                <span class="drawer__visits"><i class="bi bi-people" aria-hidden="true"></i>{{ number_format($site_visits['visites'] ?? 0, 0, ',', ' ') }} visites</span>
            @endif
        </div>

        @if(count($site['socials']))
            <div class="drawer__foot">
                @include('site.partials.socials')
            </div>
        @endif
    </aside>

    <main id="main" tabindex="-1">
        @yield('content')
        @include('sweetalert::alert')

        {{-- Notifications --}}
        @if (session('form_error'))
            <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index:1080">
                <div class="toast show ps-toast ps-toast--error" role="alert" aria-live="assertive">
                    <div class="d-flex align-items-center">
                        <div class="toast-body">
                            <i class="bi bi-exclamation-triangle-fill me-2" aria-hidden="true"></i>
                            {{ session('form_error') }}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Fermer"></button>
                    </div>
                </div>
            </div>
        @elseif (session('success_comment') || session('newsletter_success') || session('newsletter_info'))
            <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index:1080">
                <div class="toast show ps-toast {{ session('newsletter_info') ? 'ps-toast--info' : '' }}" role="status" aria-live="polite">
                    <div class="d-flex align-items-center">
                        <div class="toast-body">
                            <i class="bi bi-check-circle-fill me-2" aria-hidden="true"></i>
                            {{ session('success_comment') ?? session('newsletter_success') ?? session('newsletter_info') }}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Fermer"></button>
                    </div>
                </div>
            </div>
        @endif
    </main>

    {{-- ======= Footer ======= --}}
    <footer class="footer">
        <div class="container-xl">
            <div class="footer__grid">
                <div class="footer__brand">
                    <a href="{{ route('accueil') }}" class="footer__logo">
                        <img src="{{ $site['logo'] }}" alt="{{ config('app.name') }}" width="170" height="45" loading="lazy">
                    </a>
                    @if($site['slogan'])<p class="footer__slogan">{{ $site['slogan'] }}</p>@endif
                    @if($site['description'])<p>{{ $site['description'] }}</p>@endif
                    @if(count($site['socials']))
                        <div class="footer__social">@include('site.partials.socials')</div>
                    @endif
                    @if($site['show_visits'])
                        <div class="footer__stats" aria-label="Fréquentation du site">
                            <div><strong>{{ number_format($site_visits['visites'] ?? 0, 0, ',', ' ') }}</strong><span>visites</span></div>
                        </div>
                    @endif
                </div>

                <div>
                    <h2 class="footer__title">Thématiques</h2>
                    <ul class="footer__links">
                        @foreach ($navCategories as $item)
                            <li><a href="{{ route('post.list', ['category' => $item->slug]) }}">{{ $item->title }}</a></li>
                        @endforeach
                        <li><a href="{{ route('post.list', ['category' => 'sondage']) }}">Sondages</a></li>
                    </ul>
                </div>

                <div>
                    <h2 class="footer__title">Services santé</h2>
                    <ul class="footer__links">
                        @foreach($navServices as $route => $s)
                            <li><a href="{{ route($route) }}">{{ $s['label'] }}</a></li>
                        @endforeach
                    </ul>
                    @if(config('sante.modules.notifications'))
                        <button type="button" class="footer__notify js-push-btn" hidden>
                            <i class="bi bi-bell" aria-hidden="true"></i><span class="js-push-label">Recevoir les nouveaux articles</span>
                        </button>
                    @endif
                </div>

                <div>
                    <h2 class="footer__title">Derniers articles</h2>
                    <ul class="footer__posts">
                        @foreach ($post_last->take(3) as $item)
                            <li>
                                <a href="{{ route('post.detail', ['slug' => $item->slug]) }}">
                                    <x-media-img :model="$item" alt="" sizes="64px" width="64" height="64" />
                                    <span>
                                        <span class="footer__post-title">{{ Str::limit($item->title, 70) }}</span>
                                        <span class="footer__post-meta">{{ $item->category->title ?? '' }} · {{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}</span>
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <h2 class="footer__title">Contact</h2>
                    <ul class="footer__contact">
                        @if($site['adresse'])<li><i class="bi bi-geo-alt" aria-hidden="true"></i>{{ $site['adresse'] }}</li>@endif
                        @foreach($site['phones'] as $p)
                            <li><i class="bi bi-telephone" aria-hidden="true"></i><a href="{{ $p['href'] }}">{{ $p['value'] }}</a>@if($p['label'])<small>{{ $p['label'] }}</small>@endif</li>
                        @endforeach
                        @if($site['whatsapp_url'])
                            <li><i class="bi bi-whatsapp" aria-hidden="true"></i><a href="{{ $site['whatsapp_url'] }}" target="_blank" rel="noopener">{{ $site['whatsapp'] }}</a><small>WhatsApp</small></li>
                        @endif
                        @foreach($site['emails'] as $e)
                            <li><i class="bi bi-envelope" aria-hidden="true"></i><a href="{{ $e['href'] }}">{{ $e['value'] }}</a>@if($e['label'])<small>{{ $e['label'] }}</small>@endif</li>
                        @endforeach
                        @if($site['horaires'])<li><i class="bi bi-clock" aria-hidden="true"></i>{{ $site['horaires'] }}</li>@endif
                    </ul>
                    <a href="{{ route('contact') }}" class="footer__cta">Nous écrire <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                </div>
            </div>

            @if($site['avertissement'])
                <p class="footer__disclaimer">
                    <i class="bi bi-info-circle" aria-hidden="true"></i>
                    {{ $site['avertissement'] }}
                </p>
            @endif

            <div class="footer__legal">
                <span>&copy; {{ date('Y') }} <strong>{{ config('app.name') }}</strong>. Tous droits réservés. · <a href="{{ route('privacy') }}">Politique de confidentialité</a></span>
                <span>Développé par <a href="https://dolubux.com" target="_blank" rel="noopener noreferrer">dolubux.com</a></span>
            </div>
        </div>
    </footer>

    {{-- ======= Installer l'application (PWA) ======= --}}
    {{-- Popup d'information (Admin > Popups) --}}
    @if(($site_popup ?? null) && $site_popup->image && ($site_popup->pages === 'tout' || request()->routeIs('accueil')))
        @php
            $pp     = $site_popup;
            $ppHref = $pp->lien ? route('popup.clic', $pp->id) : null;
        @endphp
        <dialog class="popup popup--poster js-popup" aria-label="{{ $pp->titre }}"
                data-id="{{ $pp->id }}" data-frequency="{{ $pp->frequence }}" data-delay="{{ $pp->delai }}"
                data-track="{{ route('popup.vue', $pp->id) }}">
            <div class="popup__panel">
                <button type="button" class="popup__close js-popup-close" aria-label="Fermer"><i class="bi bi-x-lg" aria-hidden="true"></i></button>
                @if($ppHref)<a href="{{ $ppHref }}" class="popup__media">@else<div class="popup__media">@endif
                    <img class="popup__img" data-src="{{ $pp->image }}" alt="{{ $pp->titre }}" decoding="async">
                @if($ppHref)</a>@else</div>@endif
                <div class="popup__body">
                    <div class="popup__actions">
                        @if($ppHref)
                            <a href="{{ $ppHref }}" class="btn btn-medical">{{ $pp->bouton_texte ?: 'En savoir plus' }} <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                        @endif
                        <button type="button" class="btn btn-outline-medical js-popup-close">Fermer</button>
                    </div>
                </div>
            </div>
        </dialog>
    @endif

    <div class="install js-install" role="dialog" aria-labelledby="install-title" aria-describedby="install-text" hidden>
        <img src="{{ asset('assets_site/img/android-chrome-192x192.png') }}" alt="" class="install__icon" width="48" height="48">
        <div class="install__body">
            <strong id="install-title">Installer {{ config('app.name') }}</strong>
            <span id="install-text" class="js-install-text">Accédez aux conseils santé en un geste depuis votre écran d'accueil, même avec une connexion faible.</span>
        </div>
        <div class="install__actions">
            <button type="button" class="btn btn-medical btn-sm js-install-accept">Installer</button>
            <button type="button" class="install__later js-install-dismiss">Plus tard</button>
        </div>
    </div>

    <button type="button" class="to-top js-to-top" aria-label="Revenir en haut de la page">
        <i class="bi bi-arrow-up" aria-hidden="true"></i>
    </button>

    <!-- Vendor JS -->
    <script src="{{ asset('assets_site/vendor/bootstrap/js/bootstrap.bundle.min.js') }}" defer></script>
    <script src="{{ asset('assets_site/vendor/swiper/swiper-bundle.min.js') }}" defer></script>

    <!-- Site JS -->
    <script src="{{ asset('assets_site/js/site.js') }}?v={{ @filemtime(public_path('assets_site/js/site.js')) }}" defer></script>
    <script src="{{ asset('assets_site/js/site-services.js') }}?v={{ @filemtime(public_path('assets_site/js/site-services.js')) }}" defer></script>
    @stack('scripts')
</body>

</html>
