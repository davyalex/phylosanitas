 <section id="hero-slider" class="hero-slider">
     <div class="container-md bg-white" data-aos="fade-in">
         <div class="d-flex justify-content-between">
             <h3 class="aside-title">Actualités
             </h3>
             <a class="text-capitalize" href="/post/?category=actualites">Tous voir <i
                     class="bi bi-arrow-bar-right text-bold"></i> </a>
         </div>
         <div class="row">
             <div class="col-md-12">
                 <div class="swiper sliderFeaturedPosts">
                     <div class="swiper-wrapper">

                         @foreach ($actualite_externe as $item)
                             <div class="swiper-slide">
                                 <a href="/post/detail?slug={{ $item['slug'] }}" class="img-bg d-flex align-items-end"
                                     style="background-image: url('{{ $item->getFirstMediaUrl('image') }}'); height:200px">
                                     <div class="img-bg-inner">

                                     </div>

                                 </a>
                                 <span class="text-dark">{{ $item['title'] }}</span>

                             </div>
                         @endforeach

                     </div>
                     {{-- <div class="custom-swiper-button-next">
                            <span class="bi-chevron-right"></span>
                        </div>
                        <div class="custom-swiper-button-prev">
                            <span class="bi-chevron-left"></span>
                        </div> --}}

                     <div class="swiper-pagination"></div>
                 </div>
             </div>
         </div>
     </div>
 </section>
