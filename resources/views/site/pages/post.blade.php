@extends('site.layout')
@section('title', $category_req['title'])


<style>
    .page-item.active .page-link {
        z-index: 3;
        color: #fff !important;
        background-color: #00ACD6 !important;
        border-color: #00ACD6 !important;
        border-radius: 50%;
        padding: 6px 12px;
    }

    .page-link {
        z-index: 3;
        color: #00ACD6 !important;
        background-color: #fff;
        border-color: #007bff;
        border-radius: 50%;
        padding: 6px 12px !important;


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
        color: #212529 !important;
        opacity: 0.5 !important;
    }
</style>


@section('content')
    <!-- ======= Liste des posts en fonction de la categorie ======= -->
    <section>
        <div class="container">
            <div class="row">
                <div class="col-md-9" data-aos="fade-up">
                    <h3 class="category-title"><i class="bi bi-arrow-left"></i><a href="javascript:history.go(-1)">Retour</a>
                        <i class="bi bi-chevron-double-right "></i> Categorie: {{ $category_req['title'] }}
                    </h3>
                    <div class="col-lg-12">
                        <div class="row">


                            @foreach ($post as $item)
                                <div class="col-lg-6  ">
                                    <div class="post-entry-1 border mw-100 mh-300 bg-white">
                                        @if ($item->getFirstMediaUrl('image'))
                                            <a href="/post/detail?slug={{ $item['slug'] }}"><img
                                                    src="{{ asset($item->getFirstMediaUrl('image')) }}" loading="lazy"
                                                    alt=""
                                                    class="img-fluid"style=" width:100%; height:200px; object-fit:cover"></a>
                                        @else
                                            <a href="/post/detail?slug={{ $item['slug'] }}">
                                                <img src="{{ asset('assets_site/img/medc.jpg') }}" loading="lazy"
                                                    alt="" class="img-fluid"
                                                    style=" width:100%; height:200px; object-fit:cover"></a>
                                        @endif
                                        <div class="post-meta text-center "><span
                                                class="date text-capitalize text-white bg-danger p-1 rounded-pill ">
                                                {{ $item['category']['title'] }}</span>
                                            <span class="mx-1">&bullet;</span> <i
                                                class="bi bi-eye-fill w-100">{{ views($item)->count() }}</i>
                                            <span class="mx-1">&bullet;</span> <i
                                                class="bi bi-chat-left-quote w-100">{{ $item->commentaires->count() }}</i>
                                            <br>
                                            <span class="text-lowercase">publié
                                                {{ \Carbon\Carbon::parse($item['created_at'])->diffForHumans() }}</span>
                                            &bullet;

                                        </div>
                                        @if ($item['category']['title'] == 'Sondage')
                                            {{-- @php
                        $question_sondage = substr($item['description'], 3, -3)
                    @endphp --}}
                                            <h2 class="text-center text-justify"><a
                                                    href="/post/detail?slug={{ $item['slug'] }}">{!! Str::words($item->description, 15, '...') !!}
                                                </a>
                                            </h2>
                                        @else
                                            <h2 class="text-center text-justify"><a
                                                    href="/post/detail?slug={{ $item['slug'] }}">{{ Str::limit($item['title'], 30, '...') }}</a>
                                            </h2>
                                        @endif
                                    </div>

                                </div>
                            @endforeach



                            <!-- End Trending Section -->
                        </div>
                    </div>
                    {!! $post->appends(request()->query())->links('vendor.pagination.custom') !!}
                </div>
                <div class="col-md-3">
                    <!-- ======= Sidebar ======= -->
                    <!--  Categories -->
                    @include('site.pages.sections.sidebar')
                    <!--  Recent post -->
                </div>
            </div>
        </div>
    </section>
@endsection
