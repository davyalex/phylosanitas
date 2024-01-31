@extends('site.layout')
@section('title', 'Liste des articles')

@section('content')
    <!-- =======  Liste des Post recent limit ? sur la page d'accueil======= -->
    <section id="posts" class="posts">
        <div class="container" data-aos="fade-up">
            <div class="row g-5">


                {{-- First post --}}
                <div class="col-lg-9">
                    <div class="row">

                        @if (count($post) < 1)
                            <div class="text-center mt-4">
                                <h2>Aucun résultat pour votre recherche</h2>
                                <span>Mot recherché: {{ request('search') }} </span>
                            </div>
                        @else
                            @foreach ($post as $item)
                                <div class="col-lg-4  ">
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
                                        <div class="post-meta text-center "><span class="date text-capitalize ">
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
                        @endif






                        <!-- End Trending Section -->
                    </div>
                </div>

                <!--  Section right categorie -->
                <div class="col-md-3">

                    @include('site.pages.components.categorie')

                    {{-- @include('site.pages.components.sondage') --}}
                </div>

                {{-- <div class="col-lg-3">
              


            </div> --}}








            </div> <!-- End .row -->
        </div>
    </section> <!-- End Post Grid Section -->

@endsection
