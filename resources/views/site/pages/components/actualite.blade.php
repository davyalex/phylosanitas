 <div class="aside-block bg-white p-2">
     <div class="d-flex justify-content-between">
         <h3 class="aside-title">Actualités
        </h3>
        <a class="text-capitalize" href="/post/category?category=actualites">Tous voir <i class="bi bi-arrow-bar-right text-bold"></i> </a>
     </div>
     <!-- Slides with controls -->
     <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
         <div class="carousel-inner">
             @foreach ($actualite_externe as $item)
                 <div class="carousel-item active">
                     <a href="{{ $item['lien'] }}" target="_blank" rel="noopener noreferrer">
                         <img src="{{ asset($item->getFirstMediaUrl('image')) }}" loading="lazy" class="d-block w-100"
                             alt="...">
                         <h6 class="text-center"> {{ $item['title'] }} </h6>
                     </a>
                 </div>
             @endforeach
         </div>

         <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls"
             data-bs-slide="prev">
             <span class="carousel-control-prev-icon" aria-hidden="true"></span>
             <span class="visually-hidden">Previous</span>
         </button>
         <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls"
             data-bs-slide="next">
             <span class="carousel-control-next-icon" aria-hidden="true"></span>
             <span class="visually-hidden">Next</span>
         </button>

     </div><!-- End Slides with controls -->
 </div>
