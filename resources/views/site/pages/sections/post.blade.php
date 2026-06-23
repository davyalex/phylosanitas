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
                                          <h5 class="card-title mb-2">
                                              <a href="/post/detail?slug={{ $item['slug'] }}" 
                                                 class="text-decoration-none text-dark">
                                                  {!! Str::words($item->description, 12, '...') !!}
                                              </a>
                                          </h5>
                                      @else
                                          <h5 class="card-title mb-2">
                                              <a href="/post/detail?slug={{ $item['slug'] }}" 
                                                 class="text-decoration-none text-dark">
                                                  {{ Str::limit($item['title'], 60, '...') }}
                                              </a>
                                          </h5>
                                      @endif
                                      
                                      <div class="post-meta d-flex flex-wrap gap-3 align-items-center text-muted small mt-auto">
                                          <span title="Date de publication " style="text-transform: lowercase">
                                              <i class="bi bi-calendar3 text-medical-blue"></i>
                                              {{ \Carbon\Carbon::parse($item['created_at'])->diffForHumans() }}
                                          </span>
                                          <span title="Nombre de vues">
                                              <i class="bi bi-eye-fill text-health-green"></i>
                                              {{ views($item)->count() }}
                                          </span>
                                          <span title="Nombre de commentaires">
                                              <i class="bi bi-chat-left-quote-fill text-medical-teal"></i>
                                              {{ $item->commentaires_count }}
                                          </span>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      @endforeach
                  </div>
                  
               
                  <!-- Section Catégories & Newsletter après les posts -->
                  <div class="row g-4 mt-4">
                      <div class="col-12">
                          <h3 class="fw-bold text-medical-blue mb-4 text-center">
                              <i class="bi bi-collection-fill me-2"></i>
                              Explorez Nos Thématiques Santé
                          </h3>
                      </div>
                      
                      @foreach($category->take(6) as $cat)
                          <div class="col-lg-4 col-md-6">
                              <a href="{{ route('post.list', ['category' => $cat->slug]) }}" class="text-decoration-none">
                                  <div class="card card-medical h-100 border-0 category-card">
                                      <div class="card-body p-3">
                                          <div class="d-flex align-items-center">
                                              <div class="category-icon me-3">
                                                  @if($cat->title == 'Sondage')
                                                      <i class="bi bi-bar-chart-fill"></i>
                                                  @elseif(Str::contains(strtolower($cat->title), ['actualité', 'actualite']))
                                                      <i class="bi bi-newspaper"></i>
                                                  @elseif(Str::contains(strtolower($cat->title), ['nutrition', 'alimentation']))
                                                      <i class="bi bi-egg-fried"></i>
                                                  @elseif(Str::contains(strtolower($cat->title), ['sport', 'fitness']))
                                                      <i class="bi bi-heart-pulse-fill"></i>
                                                  @elseif(Str::contains(strtolower($cat->title), ['mental', 'psycho']))
                                                      <i class="bi bi-brain"></i>
                                                  @elseif(Str::contains(strtolower($cat->title), ['enfant', 'bébé']))
                                                      <i class="bi bi-emoji-smile-fill"></i>
                                                  @else
                                                      <i class="bi bi-file-medical-fill"></i>
                                                  @endif
                                              </div>
                                              <div class="flex-grow-1">
                                                  <h6 class="mb-1 fw-bold">{{ $cat->title }}</h6>
                                                  <small class="text-muted">
                                                      <i class="bi bi-file-earmark-text"></i>
                                                      {{ $cat->posts_count ?? 0 }} articles
                                                  </small>
                                              </div>
                                              <div>
                                                  <i class="bi bi-arrow-right-circle text-medical-blue"></i>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              </a>
                          </div>
                      @endforeach
                  </div>

                     <!-- Section Partenaires & Publicités -->
                  @include('site.pages.sections.partners')
                  

                  <!-- Newsletter & Contact -->
                  <div class="row g-4 mt-4">
                      <div class="col-lg-6">
                          <div class="card card-medical border-0 h-100" style="background: linear-gradient(135deg, var(--medical-blue) 0%, var(--medical-teal) 100%);">
                              <div class="card-body p-4 text-white">
                                  <div class="mb-3">
                                      <i class="bi bi-envelope-heart-fill" style="font-size: 2.5rem;"></i>
                                  </div>
                                  <h4 class="fw-bold mb-3">Restez Informé</h4>
                                  <p class="mb-3">
                                      Inscrivez-vous à notre newsletter et recevez les dernières actualités santé.
                                  </p>
                                  <form class="newsletter-form">
                                      <div class="input-group">
                                          <input type="email" class="form-control" placeholder="Votre email" required>
                                          <button class="btn btn-light" type="submit">
                                              <i class="bi bi-send-fill"></i>
                                          </button>
                                      </div>
                                  </form>
                              </div>
                          </div>
                      </div>

                      <div class="col-lg-6">
                          <div class="card card-medical border-0 h-100" style="background: linear-gradient(135deg, var(--health-green) 0%, var(--health-green-light) 100%);">
                              <div class="card-body p-4 text-white">
                                  <div class="mb-3">
                                      <i class="bi bi-chat-dots-fill" style="font-size: 2.5rem;"></i>
                                  </div>
                                  <h4 class="fw-bold mb-3">Besoin d'Aide ?</h4>
                                  <p class="mb-3">
                                      Notre équipe est à votre écoute pour répondre à vos questions.
                                  </p>
                                  <a href="{{ route('contact') }}" class="btn btn-light mb-3">
                                      <i class="bi bi-envelope-fill me-2"></i>Nous Contacter
                                  </a>
                                  <div class="d-flex gap-3">
                                      <a href="#" class="text-white"><i class="bi bi-facebook" style="font-size: 1.5rem;"></i></a>
                                      <a href="#" class="text-white"><i class="bi bi-twitter" style="font-size: 1.5rem;"></i></a>
                                      <a href="#" class="text-white"><i class="bi bi-instagram" style="font-size: 1.5rem;"></i></a>
                                      <a href="#" class="text-white"><i class="bi bi-linkedin" style="font-size: 1.5rem;"></i></a>
                                  </div>
                              </div>
                          </div>
                      </div>
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

<style>
.category-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
}

.category-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    background: linear-gradient(135deg, var(--medical-blue-light) 0%, var(--medical-teal-light) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.category-card:hover .category-icon {
    transform: scale(1.1);
    transition: transform 0.3s ease;
}

.newsletter-form .form-control:focus {
    box-shadow: none;
    border-color: white;
}
</style>
