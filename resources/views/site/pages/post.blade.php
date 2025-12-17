@extends('site.layout')
@section('title', $category_req['title'])


<style>
    .page-item.active .page-link {
        z-index: 3;
        color: #fff !important;
        background-color: var(--medical-blue) !important;
        border-color: var(--medical-blue) !important;
        border-radius: 50%;
        padding: 8px 14px;
        box-shadow: var(--shadow-md);
    }

    .page-link {
        z-index: 3;
        color: var(--medical-blue) !important;
        background-color: #fff;
        border-color: var(--medical-blue-light);
        border-radius: 50%;
        padding: 8px 14px !important;
        transition: var(--transition-fast);
    }

    .page-link:hover {
        background-color: var(--gray-light);
        transform: translateY(-2px);
    }

    .page-item:first-child .page-link {
        border-radius: 30% !important;
    }

    .page-item:last-child .page-link {
        border-radius: 30% !important;
    }

    .pagination li {
        padding: 3px;
    }

    .disabled .page-link {
        color: var(--gray-text) !important;
        opacity: 0.5 !important;
    }
</style>


@section('content')
    <!-- ======= Liste des posts en fonction de la categorie ======= -->
    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-9" data-aos="fade-up">
                    <h3 class="category-title"><i class="bi bi-arrow-left"></i><a href="javascript:history.go(-1)">Retour</a>
                        <i class="bi bi-chevron-double-right "></i> Categorie: {{ $category_req['title'] }}
                    </h3>
                    <div class="col-lg-12">
                        <div class="row">


                            @foreach ($post as $item)
                                <!-- ========== Start actualite  ========== -->
                                @if ($item['category']['slug'] == 'actualites')
                                    <div class="col-lg-6 mb-4">
                                        <div class="card card-medical h-100">
                                            <div class="position-relative overflow-hidden">
                                                @if ($item->getFirstMediaUrl('image'))
                                                    <a href="/post/detail?slug={{ $item['slug'] }}">
                                                        <img src="{{ asset($item->getFirstMediaUrl('image')) }}" 
                                                             loading="lazy" alt="{{ $item['title'] }}"
                                                             class="card-img-top" 
                                                             style="width:100%; height:240px; object-fit:cover;">
                                                    </a>
                                                @else
                                                    <a href="/post/detail?slug={{ $item['slug'] }}">
                                                        <img src="{{ asset('assets_site/img/medc.jpg') }}" 
                                                             loading="lazy" alt="{{ $item['title'] }}"
                                                             class="card-img-top"
                                                             style="width:100%; height:240px; object-fit:cover;">
                                                    </a>
                                                @endif
                                                <span class="badge badge-medical position-absolute top-0 start-0 m-3">
                                                    <i class="bi bi-newspaper me-1"></i>{{ $item['category']['title'] }}
                                                </span>
                                            </div>
                                            
                                            <div class="card-body d-flex flex-column">
                                                <h5 class="card-title mb-3">
                                                    <a href="/post/detail?slug={{ $item['slug'] }}" 
                                                       class="text-decoration-none text-dark">
                                                        {{ Str::limit($item['title'], 80, '...') }}
                                                    </a>
                                                </h5>
                                                
                                                <div class="post-meta d-flex flex-wrap gap-3 align-items-center text-muted small mt-auto">
                                                    <span>
                                                        <i class="bi bi-calendar3 text-medical-blue"></i>
                                                        {{ \Carbon\Carbon::parse($item['created_at'])->diffForHumans() }}
                                                    </span>
                                                    <span>
                                                        <i class="bi bi-eye-fill text-health-green"></i>
                                                        {{ views($item)->count() }}
                                                    </span>
                                                    <span>
                                                        <i class="bi bi-chat-left-quote-fill text-medical-teal"></i>
                                                        {{ $item->commentaires->count() }}
                                                    </span>
                                                    @if($item['lien'])
                                                        <a href="{{ $item['lien'] }}" target="_blank" 
                                                           class="text-medical-blue ms-auto"
                                                           title="Lien externe">
                                                            <i class="bi bi-box-arrow-up-right"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- ========== End actualite  ========== -->
                                @else
                                    <div class="col-lg-6 mb-4">
                                        <div class="card card-medical h-100">
                                            <div class="position-relative overflow-hidden">
                                                @if ($item->getFirstMediaUrl('image'))
                                                    <a href="/post/detail?slug={{ $item['slug'] }}">
                                                        <img src="{{ asset($item->getFirstMediaUrl('image')) }}" 
                                                             loading="lazy" alt="{{ $item['title'] }}"
                                                             class="card-img-top" 
                                                             style="width:100%; height:240px; object-fit:cover;">
                                                    </a>
                                                @else
                                                    <a href="/post/detail?slug={{ $item['slug'] }}">
                                                        <img src="{{ asset('assets_site/img/medc.jpg') }}" 
                                                             loading="lazy" alt="{{ $item['title'] }}"
                                                             class="card-img-top"
                                                             style="width:100%; height:240px; object-fit:cover;">
                                                    </a>
                                                @endif
                                                <span class="badge badge-health position-absolute top-0 start-0 m-3">
                                                    <i class="bi bi-{{ $item['category']['title'] == 'Sondage' ? 'bar-chart-fill' : 'file-text' }} me-1"></i>
                                                    {{ $item['category']['title'] }}
                                                </span>
                                            </div>
                                            
                                            <div class="card-body d-flex flex-column">
                                                @if ($item['category']['title'] == 'Sondage')
                                                    <h5 class="card-title mb-3">
                                                        <a href="/post/detail?slug={{ $item['slug'] }}" 
                                                           class="text-decoration-none text-dark">
                                                            {!! Str::words($item->description, 15, '...') !!}
                                                        </a>
                                                    </h5>
                                                @else
                                                    <h5 class="card-title mb-3">
                                                        <a href="/post/detail?slug={{ $item['slug'] }}" 
                                                           class="text-decoration-none text-dark">
                                                            {{ Str::limit($item['title'], 80, '...') }}
                                                        </a>
                                                    </h5>
                                                @endif
                                                
                                                <div class="post-meta d-flex flex-wrap gap-3 align-items-center text-muted small mt-auto">
                                                    <span>
                                                        <i class="bi bi-calendar3 text-medical-blue"></i>
                                                        {{ \Carbon\Carbon::parse($item['created_at'])->diffForHumans() }}
                                                    </span>
                                                    <span>
                                                        <i class="bi bi-eye-fill text-health-green"></i>
                                                        {{ views($item)->count() }}
                                                    </span>
                                                    <span>
                                                        <i class="bi bi-chat-left-quote-fill text-medical-teal"></i>
                                                        {{ $item->commentaires->count() }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach



                            <!-- End Trending Section -->
                        </div>
                    </div>
                    {!! $post->appends(request()->query())->links('vendor.pagination.custom') !!}
                </div>
                
                <div class="col-md-3">
                    <div class="sidebar-wrapper">
                        @include('site.pages.sections.sidebar')
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
