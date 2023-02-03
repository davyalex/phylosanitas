    <!-- ======= Hero Slider Section ======= -->
    <section id="hero-slider" class="hero-slider">
        <div class="container-md" data-aos="fade-in">
          <div class="row">
            <div class="col-12">
              <div class="swiper sliderFeaturedPosts">
                <div class="swiper-wrapper">
                 
  
                 @foreach ($actualite as $item)
                     
                  <div class="swiper-slide">
                    <a href="single-post.html" class="img-bg d-flex align-items-end" style="background-image: url('{{ $item ->getFirstMediaUrl('image') }}');">
                      <div class="img-bg-inner">
                        <h2>{{ $item['title'] }}</h2>
                        {{-- <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quidem neque est mollitia! Beatae minima assumenda repellat harum vero, officiis ipsam magnam obcaecati cumque maxime inventore repudiandae quidem necessitatibus rem atque.</p> --}}
                      </div>
                    </a>
                  </div>
                @endforeach
                 
                </div>
                <div class="custom-swiper-button-next">
                  <span class="bi-chevron-right"></span>
                </div>
                <div class="custom-swiper-button-prev">
                  <span class="bi-chevron-left"></span>
                </div>
  
                <div class="swiper-pagination"></div>
              </div>
            </div>
          </div>
        </div>
      </section><!-- End Hero Slider Section -->