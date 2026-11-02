
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>{{ config('app.name') }}-Dashboard-@yield('title')</title>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="description" content="@yield('title')">
  <meta property="og:image" content="@yield('image')">
  <meta name="title" content="@yield('title')">
  <meta name="url" content="@yield('url')">


  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Favicons -->
  <link href="{{ asset('assets_admin/img/favicon.png') }}" rel="icon">
  <link href="{{ asset('assets_admin/img/apple-touch-icon.png') }}" rel="apple-touch-icon">


  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <link rel="stylesheet" type="text/css" 
  href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
 <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

<script src="{{ asset('resources/js/app.js') }}" defer></script>

 <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
 <!-- Vendor CSS Files -->
  <link href="{{ asset('assets_admin/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets_admin/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('assets_admin/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets_admin/vendor/quill/quill.snow.css') }}" rel="stylesheet">
  <link href="{{ asset('assets_admin/vendor/quill/quill.bubble.css') }}" rel="stylesheet">
  <link href="{{ asset('assets_admin/vendor/remixicon/remixicon.css') }}" rel="stylesheet">
  <link href="{{ asset('assets_admin/vendor/simple-datatables/style.css') }}" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="{{ asset('assets_admin/css/style.css') }}" rel="stylesheet">
  <script src="{{ asset('assets_admin/js/jquery.min.js') }}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.0/sweetalert.min.js"></script>
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
  

  
</head>
<body>
   @guest
   @include('sweetalert::alert')

   <main>
       <div class="container">
   
         <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
           <div class="container">
             <div class="row justify-content-center">
               <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">
   
                 <div class="d-flex justify-content-center py-4">
                   <a href="{{route('accueil')}}" class="logo d-flex align-items-center w-auto">
                     <img src="{{asset('assets_site/img/logo/logo_fb.png')}}" alt="">
                     {{-- <span class="d-none d-lg-block">PhyloSanitas</span> --}}
                   </a>
                 </div><!-- End Logo -->
   
                 <div class="card mb-3">
   
                   <div class="card-body">
   
                     <div class="pt-4 pb-2">
                       <h5 class="card-title text-center pb-0 fs-4">Connexion Espace Admin</h5>
                     </div>
   
                     <form class="row g-3 needs-validation" action="{{ route('login') }}" method="POST" novalidate>
                           @csrf
                       <div class="col-12">
                         <label for="yourUsername" class="form-label">Telephone</label>
                         <div class="input-group has-validation">
                           <input type="text" name="phone" class="form-control" id="yourUsername" required>
                           <div class="invalid-feedback">Entrez votre numero de telephone!</div>
                         </div>
                       </div>
   
                       <div class="col-12">
                         <label for="yourPassword" class="form-label">Mot de passe</label>
                         <input type="password" name="password" class="form-control" id="password" required>
                         @include('admin.partials.hideShowPwd')
                         <div class="invalid-feedback">Entrez votre mot de passe!</div>
                       </div>
   
                       <div class="col-12">
                         <button class="btn btn-primary w-100" type="submit">Connexion</button>
                       </div>
                       <div class="col-12">
                         <p class="small mb-0 text-center text-bold"><a href="{{ route('accueil') }}">Retour au site web <i class="bi bi-arrow-90deg-right"></i></a></p>
                       </div>
                     </form>
   
                   </div>
                 </div>
   
               
   
               </div>
             </div>
           </div>
   
         </section>
   
       </div>
     </main><!-- End #main -->
   @endguest






  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
  <!-- Vendor JS Files -->
{{-- <script src="{{ asset('assets_admin/js/bootstrap.min.js') }}"></script> --}}
  <script src="{{ asset('assets_admin/vendor/apexcharts/apexcharts.min.js') }}"></script>
  <script src="{{ asset('assets_admin/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets_admin/vendor/chart.js/chart.min.js') }}"></script>
  <script src="{{ asset('assets_admin/vendor/echarts/echarts.min.js') }}"></script>
  <script src="{{ asset('assets_admin/vendor/quill/quill.min.js') }}"></script>
  <script src="{{ asset('assets_admin/vendor/simple-datatables/simple-datatables.js') }}"></script>
  <script src="{{ asset('assets_admin/vendor/tinymce/tinymce.min.js') }}"></script>
  <script src="{{ asset('assets_admin/vendor/php-email-form/validate.js') }}"></script>

  <!-- Template Main JS File -->
  <script src="{{ asset('assets_admin/js/main.js') }}"></script>

<script>
  @if(Session::has('message'))
  toastr.options =
  {
      "closeButton" : true,
      "progressBar" : true,
      "timeOut": "1000",

  }
          toastr.success("{{ session('message') }}");
  @endif

  @if(Session::has('error'))
  toastr.options =
  {
      "closeButton" : true,
      "progressBar" : true,
      "timeOut": "10000",
  }
          toastr.error("{{ session('error') }}");
  @endif

  @if(Session::has('info'))
  toastr.options =
  {
      "closeButton" : true,
      "progressBar" : true
  }
          toastr.info("{{ session('info') }}");
  @endif

  @if(Session::has('warning'))
  toastr.options =
  {
      "closeButton" : true,
      "progressBar" : true
  }
          toastr.warning("{{ session('warning') }}");
  @endif



</script>

{{-- toastr.options = {
"closeButton": false,
"debug": false,
"newestOnTop": false,
"progressBar": true,
"positionClass": "toast-top-right",
"preventDuplicates": true,
"onclick": null,
"showDuration": "300",
"hideDuration": "1000",
"timeOut": "5000",
"extendedTimeOut": "1000",
"showEasing": "swing",
"hideEasing": "linear",
"showMethod": "fadeIn",
"hideMethod": "fadeOut"
} --}}
 

</body>

</html>