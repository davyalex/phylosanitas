  <!-- =======  Liste des Post recent limit ? sur la page d'accueil======= -->
  <section id="posts" class="posts">
      <div class="container" data-aos="fade-up">
          <div class="row g-5">

              {{-- First post --}}
              {{-- <div class="col-lg-4">
          <div class="post-entry-1 lg">
            <a href="single-post.html"><img src="{{ asset($post_recent->getFirstMediaUrl('image'))}}" alt="" class="img-fluid"></a>
            <div class="post-meta"><span class="date">{{ $post_recent['category']['title'] }}</span> <span class="mx-1">&bullet;</span> <span>{{ \Carbon\Carbon::parse($post_recent['created_at'])->diffForHumans() }}</span> &bullet; <i class="bi bi-eye-fill w-100" >2000</i></div>
            <h2><a href="single-post.html">{{ $post_recent['title'] }}</a></h2> --}}
              {{-- <p class="mb-4 d-block">{!! Str::words($post_recent['description'],3,'....') !!}</p> --}}

              {{-- <div class="d-flex align-items-center author">
              <div class="photo"><img src="{{ asset('assets_admin/img/avatar.jpg') }}" alt="" class="img-fluid"></div>
              <div class="name">
                <h3 class="m-0 p-0">{{ $post_recent['user']['roles'][0]['name'] }}</h3>
              </div>
            </div> --}}
              {{-- </div>
        </div> --}}


              {{-- First post --}}
              <div class="col-lg-9">
                  <div class="row">


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
                                  <div class="post-meta text-center "><span class="date text-capitalize text-white bg-danger p-1 rounded-pill ">
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
                                              href="/post/detail?slug={{ $item['slug'] }}">{!! Str::words($item->description, 15, '...') !!} </a>
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

              <!--  Section right  -->
              <div class="col-md-3 py-2" style="background-color: #f2f2f2">

                @include('site.pages.sections.sidebar')

                  {{-- @include('site.pages.components.sondage') --}}
              </div>

              {{-- <div class="col-lg-3">
              


            </div> --}}








          </div> <!-- End .row -->
      </div>
  </section> <!-- End Post Grid Section -->
