<style>
  
  @media only screen and (max-width: 768px) {
   .actualite{
    display: none
   }
  }
  
  
  
  </style>  
  
  <!-- ======= Sidebar ======= -->

  <!--  posts actualite externe -->
  <div class="actualite">
      @include('site.pages.components.actualite')
  </div>
  <!--  sondage -->
  @include('site.pages.components.sondage')

  <!--  posts recent -->
  @include('site.pages.components.recent_post')

  <!--  Categories -->
  @include('site.pages.components.categorie')
