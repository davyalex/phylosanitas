@extends('site.layout')
@section('title', 'Recherche d\'articles')

@section('content')
    <!-- =======  Résultats de recherche ======= -->
    <section id="posts" class="posts section-medical-bg py-5">
        <div class="container" data-aos="fade-up">
            <div class="row g-4">

                <div class="col-lg-9">
                    
                    @if (count($post) < 1)
                        <!-- Aucun résultat -->
                        <div class="card card-medical text-center p-5">
                            <div class="card-body">
                                <div class="mb-4">
                                    <i class="bi bi-search text-medical-blue" style="font-size: 4rem;"></i>
                                </div>
                                <h2 class="text-medical-blue fw-bold mb-3">
                                    Aucun résultat trouvé
                                </h2>
                                <p class="text-muted mb-4">
                                    Aucun article ne correspond à votre recherche : 
                                    <strong class="text-medical-blue">"{{ request('query') }}"</strong>
                                </p>
                                <div class="section-health-accent p-4 rounded">
                                    <h5 class="text-health-green mb-3">
                                        <i class="bi bi-lightbulb-fill me-2"></i>
                                        Suggestions
                                    </h5>
                                    <ul class="text-start">
                                        <li>Vérifiez l'orthographe des mots-clés</li>
                                        <li>Essayez des termes plus généraux</li>
                                        <li>Utilisez moins de mots-clés</li>
                                        <li>Parcourez nos catégories dans la barre latérale</li>
                                    </ul>
                                </div>
                                <div class="mt-4">
                                    <a href="/" class="btn btn-medical me-2">
                                        <i class="bi bi-house-fill me-2"></i>
                                        Retour à l'accueil
                                    </a>
                                    <a href="javascript:history.back()" class="btn btn-health">
                                        <i class="bi bi-arrow-left me-2"></i>
                                        Page précédente
                                    </a>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Résultats trouvés -->
                        <div class="search-header mb-4">
                            <div class="card card-medical">
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                                        <div>
                                            <h2 class="text-medical-blue fw-bold mb-2">
                                                <i class="bi bi-search me-2"></i>
                                                Résultats de recherche
                                            </h2>
                                            <p class="text-muted mb-0">
                                                <strong class="text-health-green">{{ count($post) }}</strong> 
                                                {{ count($post) > 1 ? 'articles trouvés' : 'article trouvé' }} pour 
                                                <strong class="text-medical-blue">"{{ request('query') }}"</strong>
                                            </p>
                                        </div>
                                        <div>
                                            <a href="javascript:history.back()" class="btn btn-health">
                                                <i class="bi bi-arrow-left me-2"></i>
                                                Retour
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Grille des résultats -->
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
                                                    {{ $item->commentaires_count }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                </div>

                <!--  Section Sidebar -->
                <div class="col-lg-3">
                    <div class="sidebar-wrapper">
                        @include('site.pages.sections.sidebar')
                    </div>
                </div>

            </div> <!-- End .row -->
        </div>
    </section> <!-- End Search Results Section -->

@endsection
