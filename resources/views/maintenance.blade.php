
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

    <main>
        <div class="container">
    
          <section class="section error-404 min-vh-100 d-flex flex-column align-items-center justify-content-center">
            <h2>Nous sommes en mode maintenance <br>Veuillez ressayer plutard!</h2>
            <img src="{{ asset('assets_admin/img/not-found.svg') }}" class="img-fluid py-5" alt="Page Not Found">
           
          </section>
    
        </div>
      </main><!-- End #main -->


  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>{{ config('app.name') }}</span></strong>. Tous droits reservés
    </div>
    <div class="credits">
     
      Crée par <a href="">Labodigit</a>
    </div>
  </footer><!-- End Footer -->

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