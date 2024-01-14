  <!-- ======= Sidebar ======= -->

  <!--  posts recent -->
  @include('site.pages.components.recent_post')

 <!--  Categories -->

@include('site.pages.components.categorie')

{{-- @include('site.pages.components.sondage') --}}

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
