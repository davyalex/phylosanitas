  <!-- ======= Post Grid Section ======= -->
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
        <div class="col-lg-10">
          <div class="row">

              
              @foreach ($post as $item)
              <div class="col-lg-4 mb-0 border border-white">
              <div class="post-entry-1 border">
                @if ($item->getFirstMediaUrl('image'))
                <a href="/post/detail?slug={{ $item['slug'] }}"><img src="{{ asset($item->getFirstMediaUrl('image'))}}" alt="" class="img-fluid"style=" width:auto; height:300px; object-fit:contain"></a>
                @else
                <a href="/post/detail?slug={{ $item['slug'] }}">
                  <img src="{{ asset('assets_site/img/medc.jpg')}}" alt="" class="img-fluid" style=" width:auto; height:300px; object-fit:contain"></a>
                @endif
                <div class="post-meta text-center "><span class="date text-capitalize "> {{ $item['category']['title'] }}</span>
                     <span class="mx-1">&bullet;</span> <i class="bi bi-eye-fill w-100" >2000</i>
                     <span class="mx-1">&bullet;</span> <i class="bi bi-chat-left-quote w-100" >{{ $item->commentaires->count() }}</i>
                     <br>
                     <span class="text-lowercase">publié {{ \Carbon\Carbon::parse($post_recent['created_at'])->diffForHumans() }}</span> 
                     &bullet; 
                    
                    </div>
                <h2 class="text-center text-justify"><a href="/post/detail?slug={{ $item['slug'] }}">{{ Str::limit($item['title'], 20, '...') }}</a></h2>
              </div>
           
            </div>
            @endforeach

         
          
            <!-- End Trending Section -->
          </div>
        </div>

          <!--  Section right -->
          <div class="col-lg-2">

           
            <div class="aside-block">
              <h3 class="aside-title">Tags &amp;Categories</h3>
              <ul class="aside-tags list-unstyled">
                @foreach ($category as $item)
                <li><a class="text-capitalize" href="/post/category?category={{ $item['slug'] }}">{{ $item['title'] }} <span class="badge rounded-pill bg-info">{{ $item->posts->count() }}</span></a></li>
                @endforeach
               
              </ul>
            </div><!-- End Tags -->
          </div> 
          

      </div> <!-- End .row -->
    </div>
  </section> <!-- End Post Grid Section -->