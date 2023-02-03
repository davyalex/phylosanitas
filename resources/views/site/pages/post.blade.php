@extends('site.layout')
@section('title',$category_req['title'] )


<style>
  .page-item.active .page-link{
      z-index: 3;
      color: #fff !important  ;
      background-color: #00ACD6 !important;
      border-color: #00ACD6 !important;
      border-radius: 50%;
      padding: 6px 12px;
  }
  .page-link{
      z-index: 3;
      color: #00ACD6 !important;
      background-color: #fff;
      border-color: #007bff;
      border-radius: 50%;
      padding: 6px 12px !important;
      
      
  }
  .page-item:first-child .page-link{
      border-radius: 30% !important;
  }
  .page-item:last-child .page-link{
      border-radius: 30% !important;   
  }
  .pagination li{
      padding: 3px;
  }

  .disabled .page-link{
      color: #212529 !important;
      opacity: 0.5 !important;
  }
</style>


@section('content')
<section>
    <div class="container">
      <div class="row">

        <div class="col-md-9" data-aos="fade-up">
            <h3 class="category-title"><i class="bi bi-arrow-left"></i><a href="javascript:history.go(-1)">Retour</a>
              <i class="bi bi-chevron-double-right "></i> Categorie: {{ $category_req['title'] }}</h3>


          @foreach ($post as $item)
              
          <div class="d-md-flex post-entry-2 half">
           
            @if ($item->getFirstMediaUrl('image'))
            <a class="me-4 thumbnail" href="/post/detail?slug={{ $item['slug'] }}"><img src="{{ asset($item->getFirstMediaUrl('image'))}}" alt="" class="img-fluid"style=" width:auto; height:300px; object-fit:contain"></a>
            @else
            <a class="me-4 thumbnail" href="/post/detail?slug={{ $item['slug'] }}">
              <img src="{{ asset('assets_site/img/medc.jpg')}}" alt="" class="img-fluid" style=" width:auto; height:300px; object-fit:contain"></a>
            @endif
            <div>
              <div class="post-meta"><span class="date">{{ $item['category']['title'] }}</span> <span class="mx-1">&bullet;</span> <span>publié {{ \Carbon\Carbon::parse($item['created_at'])->diffForHumans() }}</span>
          
            </div>
            <div class="post-meta d-flex justify-content-around ">
                <h6 class="text-capitalize mr-auto"><i class="bi bi-eye"> </i> {{ views($item)->count() }} vues</h6> 
                <h6 class="text-capitalize "><i class="bi bi-chat-left-quote"> </i>{{ $item->commentaires->count() }} commentaires</h6>
            </div>
              <h3><a href="/post/detail?slug={{ $item['slug'] }}">{{ $item['title'] }}</a></h3>
              <p>{!! Str::words($item['description'],60,'...') !!}</p>
              {{-- <div class="d-flex align-items-center author">
                <div class="photo"><img src="assets/img/person-2.jpg" alt="" class="img-fluid"></div>
                <div class="name">
                  <h3 class="m-0 p-0">Wade Warren</h3>
                </div>
              </div> --}}
            
            </div>
           
          </div>
          @endforeach
         
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