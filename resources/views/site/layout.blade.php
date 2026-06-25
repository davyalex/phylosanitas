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

    <meta charset="utf-8">
    <meta http-equiv="X-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="@yield('description', config('app.name') . ' — Blog médical et santé')">
    <meta name="title" content="@yield('title', config('app.name'))">
    <meta property="og:title" content="@yield('title', config('app.name'))">
    <meta property="og:description" content="@yield('description', 'Blog médical et santé')">
    <meta property="og:image" content="@yield('image', asset('assets_site/img/logo/logo_tp.png'))">
    <meta property="og:url" content="@yield('url', url()->current())">
    <meta property="og:type" content="website">
    <meta name="google-adsense-account" content="ca-pub-6925205610540207">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }} — @yield('title', 'Accueil')</title>

    <!-- Favicons -->
    <link rel="preload" as="image" href="{{ asset('assets_site/img/logo/logo_tp.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets_site/img/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets_site/img/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets_site/img/favicon-16x16.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;500&family=Inter:wght@400;500&family=Playfair+Display:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">

    <!-- Vendor CSS -->
    <link href="{{ asset('assets_site/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets_site/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets_site/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets_site/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets_site/vendor/aos/aos.css') }}" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="{{ asset('assets_site/css/variables.css') }}" rel="stylesheet">
    <link href="{{ asset('assets_site/css/main.css') }}" rel="stylesheet">
    <link href="{{ asset('assets_site/css/phylosanitas-theme.css') }}" rel="stylesheet">

    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-6925205610540207" crossorigin="anonymous"></script>
</head>

<body>
    <!-- Google Tag Manager (noscript) -->
    <noscript>
        <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-P2HGDT42" height="0" width="0" style="display:none;visibility:hidden"></iframe>
    </noscript>

    <!-- ======= Header ======= -->
    <header id="header" class="header d-flex align-items-center fixed-top">

        <!-- Barre supérieure -->
        <div class="container-fluid fixed-top header-top-medical mb-2">
            <div class="row">
                <div class="col-md-12 text-center">
                    <a href="https://www.facebook.com/phylosanitas" class="mx-2 text-white" target="_blank" rel="noopener noreferrer">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="#" class="mx-2 text-white"><i class="bi bi-twitter"></i></a>
                    <a href="#" class="mx-2 text-white"><i class="bi bi-instagram"></i></a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-white ms-3">
                            <i class="bi bi-grid me-1"></i>Dashboard
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Logo + Nav -->
        <div class="container-fluid container-xl d-flex align-items-center justify-content-between" style="margin-top:45px; padding-top:25px; padding-bottom:20px;">

            <a href="{{ route('accueil') }}" class="logo d-flex align-items-center ms-3">
                <img src="{{ asset('assets_site/img/logo/logo_tp.png') }}" alt="{{ config('app.name') }}">
            </a>

            <nav id="navbar" class="navbar">
                <ul>
                    <li>
                        <a href="{{ route('accueil') }}" class="{{ request()->routeIs('accueil') ? 'active' : '' }}">
                            <i class="bi bi-house-door me-1"></i>Accueil
                        </a>
                    </li>

                    @foreach ($category->filter(fn($c) => !in_array(strtolower($c->slug), ['sondage', 'actualites'])) as $item)
                        <li>
                            <a class="text-capitalize {{ request('category') === $item->slug ? 'active' : '' }}"
                               href="{{ route('post.list', ['category' => $item->slug]) }}">
                                {{ $item->title }}
                            </a>
                        </li>
                    @endforeach

                    <li>
                        <a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}">
                            <i class="bi bi-envelope me-1"></i>Contact
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="position-relative d-flex align-items-center gap-1">
                <a href="#" class="mx-2 js-search-open"><i class="bi bi-search"></i></a>
                <i class="bi bi-list mobile-nav-toggle"></i>

                <!-- Formulaire de recherche -->
                <div class="search-form-wrap js-search-form-wrap">
                    <form action="{{ route('search') }}" class="search-form" method="POST">
                        @csrf
                        <button type="submit" class="icon bi-search" hidden></button>
                        <input type="text" name="query" placeholder="Rechercher un article..." class="form-control" minlength="2">
                        <button class="btn js-search-close"><i class="bi bi-x"></i></button>
                    </form>
                </div>
            </div>

        </div>
    </header><!-- End Header -->

    <main id="main">
        @yield('content')
        @include('sweetalert::alert')

        {{-- Notification commentaire --}}
        @if (session('success_comment'))
            <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index:9999">
                <div class="toast show align-items-center text-white bg-success border-0" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success_comment') }}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Notification newsletter --}}
        @if (session('newsletter_success') || session('newsletter_info'))
            <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index:9999">
                <div class="toast show align-items-center text-white {{ session('newsletter_success') ? 'bg-success' : 'bg-info' }} border-0" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="bi bi-envelope-check-fill me-2"></i>
                            {{ session('newsletter_success') ?? session('newsletter_info') }}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            </div>
        @endif
    </main>

    <!-- ======= Footer ======= -->
    <footer id="footer" class="footer">
        <div class="footer-content">
            <div class="container">
                <div class="row g-5">
                    <div class="col-6 col-lg-3">
                        <h3 class="footer-heading">Menu</h3>
                        <ul class="footer-links list-unstyled">
                            <li><a href="{{ route('accueil') }}"><i class="bi bi-chevron-right"></i> Accueil</a></li>
                            @foreach ($category->filter(fn($c) => !in_array(strtolower($c->slug), ['sondage', 'actualites'])) as $item)
                                <li>
                                    <a href="{{ route('post.list', ['category' => $item->slug]) }}">
                                        <i class="bi bi-chevron-right"></i> {{ $item->title }}
                                    </a>
                                </li>
                            @endforeach
                            <li><a href="{{ route('contact') }}"><i class="bi bi-chevron-right"></i> Contact</a></li>
                        </ul>
                    </div>

                    <div class="col-6 col-lg-3">
                        <h3 class="footer-heading">Catégories</h3>
                        <ul class="footer-links list-unstyled">
                            @foreach ($category->filter(fn($c) => !in_array(strtolower($c->slug), ['sondage', 'actualites'])) as $item)
                                <li>
                                    <a href="{{ route('post.list', ['category' => $item->slug]) }}">
                                        <i class="bi bi-chevron-right"></i> {{ $item->title }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="col-lg-4">
                        <h3 class="footer-heading">Derniers articles</h3>
                        <ul class="footer-links footer-blog-entry list-unstyled">
                            @foreach ($post_last as $item)
                                <li>
                                    <a href="{{ route('post.detail', ['slug' => $item->slug]) }}" class="d-flex align-items-center">
                                        @if ($item->getFirstMediaUrl('image'))
                                            <img src="{{ asset($item->getFirstMediaUrl('image')) }}" alt="{{ $item->title }}" class="img-fluid me-3">
                                        @else
                                            <img src="{{ asset('assets_site/img/medc.jpg') }}" alt="{{ $item->title }}" class="img-fluid me-3">
                                        @endif
                                        <div>
                                            <div class="post-meta d-block">
                                                <span class="date">{{ $item->category->title ?? '' }}</span>
                                                <span class="mx-1">&bullet;</span>
                                                <span>{{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}</span>
                                            </div>
                                            <span>{{ Str::limit($item->title, 50) }}</span>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-legal">
            <div class="container">
                <div class="row justify-content-between">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        <div class="copyright">
                            Copyright &copy; {{ date('Y') }} <strong>{{ config('app.name') }}</strong>. Tous droits réservés.
                        </div>
                        <div class="credits">
                            Développé par <a href="https://dolubux.com" target="_blank" rel="noopener noreferrer">dolubux.com</a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="social-links mb-3 mb-lg-0 text-center text-md-end">
                            <a href="#" class="twitter"><i class="bi bi-twitter"></i></a>
                            <a href="https://www.facebook.com/phylosanitas" class="facebook" target="_blank" rel="noopener noreferrer"><i class="bi bi-facebook"></i></a>
                            <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
                            <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <a href="{{ route('accueil') }}" class="home-button d-flex align-items-center justify-content-center">
        <i class="bi bi-house-fill"></i>
    </a>
    <a href="#" class="scroll-top d-flex align-items-center justify-content-center">
        <i class="bi bi-arrow-up-short"></i>
    </a>

    <!-- Vendor JS -->
    <script src="{{ asset('assets_site/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets_site/vendor/swiper/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('assets_site/vendor/glightbox/js/glightbox.min.js') }}"></script>
    <script src="{{ asset('assets_site/vendor/aos/aos.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('assets_site/js/main.js') }}"></script>

    <!-- Auto-dismiss toasts -->
    <script>
        document.querySelectorAll('.toast').forEach(function(toast) {
            setTimeout(function() { toast.classList.remove('show'); }, 5000);
        });
    </script>
</body>

</html>
