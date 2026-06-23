@extends('site.layout')
@section('title', $category_req ? $category_req->title : 'Articles')
@section('description', 'Parcourez les articles de la catégorie ' . ($category_req ? $category_req->title : 'santé') . ' sur PhyloSanitas.')

@section('content')
<section class="py-5 mt-4">
    <div class="container">
        <div class="row g-4">

            {{-- Colonne principale --}}
            <div class="col-md-9" data-aos="fade-up">

                {{-- En-tête catégorie --}}
                <div class="d-flex align-items-center gap-3 mb-4 mt-2">
                    <a href="javascript:history.go(-1)" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Retour
                    </a>
                    @if ($category_req)
                        <h3 class="mb-0 text-medical-blue fw-bold text-capitalize">
                            <i class="bi bi-collection-fill me-2"></i>{{ $category_req->title }}
                        </h3>
                    @else
                        <h3 class="mb-0 text-medical-blue fw-bold">
                            <i class="bi bi-newspaper me-2"></i>Tous les articles
                        </h3>
                    @endif
                    <span class="badge badge-medical ms-auto">{{ $post->total() }} article(s)</span>
                </div>

                @if ($post->isEmpty())
                    <div class="card card-medical text-center p-5">
                        <i class="bi bi-journal-x text-medical-blue" style="font-size:3rem;"></i>
                        <h5 class="mt-3 text-muted">Aucun article dans cette catégorie pour le moment.</h5>
                        <a href="{{ route('accueil') }}" class="btn btn-medical mt-3">
                            <i class="bi bi-house-fill me-2"></i>Retour à l'accueil
                        </a>
                    </div>
                @else
                    <div class="row g-4">
                        @foreach ($post as $item)
                            <div class="col-lg-6 mb-2">
                                <div class="card card-medical h-100">
                                    <div class="position-relative overflow-hidden">
                                        <a href="{{ route('post.detail', ['slug' => $item->slug]) }}">
                                            <img src="{{ $item->getFirstMediaUrl('image') ?: asset('assets_site/img/medc.jpg') }}"
                                                 loading="lazy"
                                                 alt="{{ $item->title }}"
                                                 class="card-img-top post-card-img">
                                        </a>
                                        <span class="badge position-absolute top-0 start-0 m-3 {{ strtolower($item->category->slug ?? '') === 'actualites' ? 'badge-medical' : 'badge-health' }}">
                                            <i class="bi bi-{{ strtolower($item->category->slug ?? '') === 'actualites' ? 'newspaper' : 'file-text' }} me-1"></i>
                                            {{ $item->category->title ?? '' }}
                                        </span>
                                        @if ($item->lien)
                                            <a href="{{ $item->lien }}" target="_blank" rel="noopener noreferrer"
                                               class="position-absolute top-0 end-0 m-3 badge badge-medical"
                                               title="Lien externe">
                                                <i class="bi bi-box-arrow-up-right"></i>
                                            </a>
                                        @endif
                                    </div>

                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title mb-3">
                                            <a href="{{ route('post.detail', ['slug' => $item->slug]) }}"
                                               class="text-decoration-none text-dark">
                                                {{ Str::limit($item->title ?? strip_tags($item->description), 80, '…') }}
                                            </a>
                                        </h5>

                                        <div class="post-meta d-flex flex-wrap gap-3 align-items-center text-muted small mt-auto">
                                            <span>
                                                <i class="bi bi-calendar3 text-medical-blue"></i>
                                                {{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}
                                            </span>
                                            <span>
                                                <i class="bi bi-chat-left-quote-fill text-medical-teal"></i>
                                                {{ $item->commentaires_count ?? 0 }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="d-flex justify-content-center mt-5">
                        {!! $post->appends(request()->query())->links('vendor.pagination.custom') !!}
                    </div>
                @endif

            </div>{{-- End col-md-9 --}}

            {{-- Sidebar --}}
            <div class="col-md-3">
                <div class="sidebar-wrapper">
                    @include('site.pages.sections.sidebar')
                </div>
            </div>

        </div>
    </div>
</section>
@endsection
