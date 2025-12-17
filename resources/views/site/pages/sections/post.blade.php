  <!-- =======  Liste des Post récents sur la page d'accueil======= -->
  <section id="posts" class="posts section-medical-bg py-5">
      <div class="container" data-aos="fade-up">
          <div class="row g-4">

              <div class="col-md-9 col-lg-9">
                  <div class="mb-4">
                      <h2 class="text-medical-blue fw-bold mb-2">
                          <i class="bi bi-newspaper me-2"></i>
                          Dernières Publications
                      </h2>
                      <p class="text-muted">Découvrez nos derniers articles et actualités santé</p>
                  </div>
                  
                  <div class="row g-4">
                      @foreach ($post as $item)
                          <div class="col-lg-4 col-md-6">
                              <div class="card card-medical h-100">
                                  <div class="position-relative overflow-hidden">
                                      @if ($item->getFirstMediaUrl('image'))
                                          <a href="/post/detail?slug={{ $item['slug'] }}">
                                              <img src="{{ asset($item->getFirstMediaUrl('image')) }}" 
                                                   loading="lazy" 
                                                   alt="{{ $item['title'] }}"
                                                   class="card-img-top" 
                                                   style="width:100%; height:220px; object-fit:cover;">
                                          </a>
                                      @else
                                          <a href="/post/detail?slug={{ $item['slug'] }}">
                                              <img src="{{ asset('assets_site/img/medc.jpg') }}" 
                                                   loading="lazy" 
                                                   alt="{{ $item['title'] }}"
                                                   class="card-img-top"
                                                   style="width:100%; height:220px; object-fit:cover;">
                                          </a>
                                      @endif
                                      
                                      <span class="badge position-absolute top-0 start-0 m-3 {{ $item['category']['title'] == 'Sondage' ? 'badge-health' : 'badge-medical' }}">
                                          <i class="bi bi-{{ $item['category']['title'] == 'Sondage' ? 'bar-chart-fill' : 'newspaper' }} me-1"></i>
                                          {{ $item['category']['title'] }}
                                      </span>
                                  </div>
                                  
                                  <div class="card-body d-flex flex-column">
                                      @if ($item['category']['title'] == 'Sondage')
                                          <h5 class="card-title mb-3" style="min-height: 60px;">
                                              <a href="/post/detail?slug={{ $item['slug'] }}" 
                                                 class="text-decoration-none text-dark">
                                                  {!! Str::words($item->description, 12, '...') !!}
                                              </a>
                                          </h5>
                                      @else
                                          <h5 class="card-title mb-3" style="min-height: 60px;">
                                              <a href="/post/detail?slug={{ $item['slug'] }}" 
                                                 class="text-decoration-none text-dark">
                                                  {{ Str::limit($item['title'], 60, '...') }}
                                              </a>
                                          </h5>
                                      @endif
                                      
                                      <div class="post-meta d-flex flex-wrap gap-3 align-items-center text-muted small mt-auto">
                                          <span title="Date de publication">
                                              <i class="bi bi-calendar3 text-medical-blue"></i>
                                              {{ \Carbon\Carbon::parse($item['created_at'])->diffForHumans() }}
                                          </span>
                                          <span title="Nombre de vues">
                                              <i class="bi bi-eye-fill text-health-green"></i>
                                              {{ views($item)->count() }}
                                          </span>
                                          <span title="Nombre de commentaires">
                                              <i class="bi bi-chat-left-quote-fill text-medical-teal"></i>
                                              {{ $item->commentaires->count() }}
                                          </span>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      @endforeach
                  </div>
              </div>

              <!--  Section Sidebar  -->
              <div class="col-md-3 col-lg-3">
                  <div class="sidebar-wrapper">
                      @include('site.pages.sections.sidebar')
                  </div>
              </div>

          </div> <!-- End .row -->
      </div>
  </section> <!-- End Post Grid Section -->
