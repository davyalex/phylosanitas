<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="@yield('title')">
  <meta property="og:image" content="@yield('image')">
  <meta name="title" content="@yield('title')">
  <meta name="url" content="@yield('url')">


  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title> {{ config('app.name') }}-@yield('title') </title>

 
 {{-- @yield('meta') --}}



  <!-- Favicons -->
  <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
  <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets_site/img/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets_site/img/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets_site/img/favicon-16x16.png') }}">
<link rel="manifest" href="/site.webmanifest">
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;500&family=Inter:wght@400;500&family=Playfair+Display:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="{{ asset('assets_site/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets_site/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('assets_site/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets_site/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets_site/vendor/aos/aos.css') }}" rel="stylesheet">

  <!-- Template Main CSS Files -->
  <link href="{{ asset('assets_site/css/variables.css') }}" rel="stylesheet">
  <link href="{{ asset('assets_site/css/main.css') }}" rel="stylesheet">


</head>

<body>
  {{-- <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="{{ asset('assets_site/img/preload.png') }}" alt="AdminLTELogo"
        height="60" width="60">
</div> --}}
  <!-- ======= Header ======= -->
  <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid fixed-top bg-info mb-2">
      <div class="row">
        <div class="col-md-12 text-center">
          <a href="#" class="mx-2 text-white"><span class="bi-facebook"></span></a>
          <a href="#" class="mx-2 text-white"><span class="bi-twitter"></span></a>
          <a href="#" class="mx-2 text-white"><span class="bi-instagram"></span></a>
        </div>
      </div>
    </div>
    <div class="container-fluid container-xl d-flex align-items-center justify-content-between">

      <!-- logo -->

      <a href="{{ route('accueil') }}" class="logo d-flex align-items-center mt-3">
        <!-- Uncomment the line below if you also wish to use an image logo -->
         <img src="{{ asset('assets_site/img/logo/logo_tp.png') }}"  alt=""> 
        {{-- <h1 class="text-info">Φ</h1> <h3 class="text-danger m-1">S</h3>
        <h2 class="m-2"> 
          <span class="text-info">Phylo</span><span class="text-danger">Sanitas</span></h2> --}}
      </a>

      <!-- menu -->
      <nav id="navbar" class="navbar mt-3">
        <ul>
          <li><a href="{{ route('accueil') }}">Accueil</a></li>
          @foreach ($category as $item)
          <li><a class="text-capitalize" href="/post/category?category={{ $item['slug'] }}">{{ $item['title'] }}</a></li>
          @endforeach
          





          <!-- <li><a href="single-post.html">Single Post</a></li> -->
          <!-- <li class="dropdown"><a href="category.html"><span>Categories</span> <i class="bi bi-chevron-down dropdown-indicator"></i></a>
            <ul>
              <li><a href="search-result.html">Search Result</a></li>
              <li><a href="#">Drop Down 1</a></li>
              <li class="dropdown"><a href="#"><span>Deep Drop Down</span> <i class="bi bi-chevron-down dropdown-indicator"></i></a>
                <ul>
                  <li><a href="#">Deep Drop Down 1</a></li>
                  <li><a href="#">Deep Drop Down 2</a></li>
                  <li><a href="#">Deep Drop Down 3</a></li>
                  <li><a href="#">Deep Drop Down 4</a></li>
                  <li><a href="#">Deep Drop Down 5</a></li>
                </ul>
              </li>
              <li><a href="#">Drop Down 2</a></li>
              <li><a href="#">Drop Down 3</a></li>
              <li><a href="#">Drop Down 4</a></li>
            </ul>
          </li>

          <li><a href="about.html">About</a></li> -->
          <li><a href="{{ route('contact') }}">Contact</a></li>
          @auth
          <li><a href="{{ route('dashboard') }}"> <i class="bi bi-grid"></i> Dashboard</a></li>
          @endauth
        </ul>
      </nav><!-- .navbar -->

      <!-- lien reseaux sociaux -->
      <div class="position-relative mt-3">
        <!-- <a href="#" class="mx-2"><span class="bi-facebook"></span></a>
        <a href="#" class="mx-2"><span class="bi-twitter"></span></a>
        <a href="#" class="mx-2"><span class="bi-instagram"></span></a> -->

        <a href="#" class="mx-2 js-search-open mt-3"><span class=""></span></a>
        <i class="bi bi-list mobile-nav-toggle"></i>

        <!-- ======= Search Form ======= -->
        <div class="search-form-wrap js-search-form-wrap">
          <form action="" class="search-form">
            <span class="icon bi-search"></span>
            <input type="text" placeholder="Search" class="form-control">
            <button class="btn js-search-close"><span class="bi-x"></span></button>
          </form>
        </div><!-- End Search Form -->

      </div>

    </div>

  </header><!-- End Header -->

  <main id="main">


  @yield('content')
  @include('sweetalert::alert')



  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">

    <div class="footer-content">
      <div class="container">

        <div class="row g-5">
          <!-- <div class="col-lg-4">
            <h3 class="footer-heading">About PhyloSanitas</h3>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Magnam ab, perspiciatis beatae autem deleniti voluptate nulla a dolores, exercitationem eveniet libero laudantium recusandae officiis qui aliquid blanditiis omnis quae. Explicabo?</p>
            <p><a href="about.html" class="footer-link-more">Learn More</a></p>
          </div> -->
          <div class="col-6 col-lg-3">
            <h3 class="footer-heading">Menu</h3>
            <ul class="footer-links list-unstyled">
              <li><a href="{{ route('accueil') }}"><i class="bi bi-chevron-right"></i> Accueil</a></li>
                @foreach ($category as $item)
                    <li><a href="/post/category?category={{ $item['slug'] }}"><i class="bi bi-chevron-right"></i>  {{ $item['title'] }}</a></li>
                @endforeach
                <li><a href=""><i class="{{ route('contact') }}"></i> Contact</a></li>
            </ul>
          </div>
          <div class="col-6 col-lg-3">
            <h3 class="footer-heading">Categories</h3>
            <ul class="footer-links list-unstyled">
                @foreach ($category as $item)
                <li><a href="/post/category?category={{ $item['slug'] }}"><i class="bi bi-chevron-right"></i>  {{ $item['title'] }}</a></li>
            @endforeach

            </ul>
          </div>

          <div class="col-lg-4">
            <h3 class="footer-heading">Les derniers posts</h3>

            <ul class="footer-links footer-blog-entry list-unstyled">
        @foreach ($post_last as $item)
            
       
              <li>
                <a href="/post/detail?slug={{ $item['slug'] }}" class="d-flex align-items-center">
                  @if ($item->getFirstMediaUrl('image'))
                  
                  <img src="{{ asset($item->getFirstMediaUrl('image')) }}" alt="" class="img-fluid me-3">
                  @else
                  <img src="{{ asset('assets_site/img/medc.jpg') }}" alt="" class="img-fluid me-3">
   
                  @endif
                  <div>
                    <div class="post-meta d-block"><span class="date">{{ $item['category']['title'] }}</span> <span class="mx-1">&bullet;</span> <span>{{ \Carbon\Carbon::parse($item['created_at'])->diffForHumans() }}</span></div>
                    <span>{{ $item['title'] }}</span>
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
               Copyright © <?php echo date('Y') ?> <strong><span>{{ config('app.name') }}</span></strong>. Tous droits reservés
            </div>

            <div class="credits">
              Développé par<a href="https://dolubux.com" target="_blank"> dolubux.com</a>
            </div>

          </div>

          <div class="col-md-6">
            <div class="social-links mb-3 mb-lg-0 text-center text-md-end">
              <a href="#" class="twitter"><i class="bi bi-twitter"></i></a>
              <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
              <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
              <a href="#" class="google-plus"><i class="bi bi-skype"></i></a>
              <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
            </div>

          </div>

        </div>

      </div>
    </div>

  </footer>

  <a href="#" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="{{ asset('assets_site/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets_site/vendor/swiper/swiper-bundle.min.js') }}"></script>
  <script src="{{ asset('assets_site/vendor/glightbox/js/glightbox.min.js') }}"></script>
  <script src="{{ asset('assets_site/vendor/aos/aos.js') }}"></script>
  <script src="{{ asset('assets_site/vendor/php-email-form/validate.js') }}"></script>

  <!-- Template Main JS File -->
  <script src="{{ asset('assets_site/js/main.js') }}"></script>

</body>

</html>