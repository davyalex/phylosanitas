  <!-- ======= Sidebar ======= -->
   
  <div class="aside-block back-to-top mt-4">
    <h3 class="aside-title">Les recents posts</h3>
    @foreach ($post_last as $item)
          <div class="post-entry-1 border-bottom">
            <div class="post-meta">
              <span class="date">{{ $item['category']['title'] }}</span> <span class="mx-1">&bullet;</span> <span>publié {{ \Carbon\Carbon::parse($item['created_at'])->diffForHumans() }}</span>
            </div>    
              <div class="d-flex align-items-center">
                <img src="{{ asset( $item->getFirstMediaUrl('image')) }}" height="50px" width="50px" alt="">
            <h2 class="mb-2 mx-2"><a href="/post/detail?slug={{ $item['slug'] }}">{{ $item['title'] }}</a></h2>
              </div>
    {{-- <span class="author mb-3 d-block">Jenny Wilson</span> --}}
  </div>
    @endforeach

</div><!-- End posts recent -->

  <div class="aside-block">
    <h3 class="aside-title">Categories</h3>
    <ul class="aside-links list-unstyled">
        @foreach ($category as $item)
        <li><a href="/post/category?category={{ $item['slug'] }}"><i class="bi bi-chevron-right"></i> {{ $item['title'] }} <span class="badge rounded-pill bg-info">{{ $item->posts->count() }}</span></a> </li>
        @endforeach
    </ul>
  </div><!-- End Categories -->
  


  {{-- <div class="">
    <h3 class="footer-heading">Les derniers posts</h3>

    <ul class="footer-links footer-blog-entry list-unstyled">
@foreach ($post_last as $item)
    

      <li>
        <a href="single-post.html" class="d-flex align-items-center">
          @if ($item->getFirstMediaUrl('image'))
          
          <img src="{{ asset($item->getFirstMediaUrl('image')) }}" alt="" class="img-fluid me-3">
          @else
          <img src="{{ asset('assets_site/img/medc.jpg') }}" alt="" class="img-fluid me-3">

          @endif
          <div>
            <div class="post-meta d-block"><span class="date">{{ $item['category']['title'] }}</span> <span class="mx-1">&bullet;</span> <span>Jul 5th '22</span></div>
            <span>{{ $item['title'] }}</span>
          </div>
        </a>
      </li>
@endforeach

    </ul>

  </div>
  --}}