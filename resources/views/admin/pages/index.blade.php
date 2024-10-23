@extends('admin.layout')
@section('title', 'Accueil')

@section('content')
<section class="section dashboard">
    <div class="row">

      <!-- Left side columns -->
      <div class="col-lg-12">
        <div class="row">

          <!-- Sales Card -->
          <div class="col-xxl-4 col-md-4">
            <div class="card info-card sales-card">


              <div class="card-body">
                <h5 class="card-title">Posts</h5>

                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-card-text"></i>
                  </div>
                  <div class="ps-3">
                    
                    <h6>{{ $post_count }}</h6>
                    {{-- <span class="text-success small pt-1 fw-bold">12%</span> <span class="text-muted small pt-2 ps-1">increase</span> --}}

                  </div>
                </div>
              </div>

            </div>
          </div><!-- End Sales Card -->

          <!-- Revenue Card -->
          <div class="col-xxl-4 col-md-4">
            <div class="card info-card revenue-card">
              <div class="card-body">
                <h5 class="card-title">Categories</h5>

                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-card-text"></i>
                  </div>
                  <div class="ps-3">
                    <h6>{{ $category_count }}</h6>
                    {{-- <span class="text-success small pt-1 fw-bold">8%</span> <span class="text-muted small pt-2 ps-1">increase</span> --}}

                  </div>
                </div>
              </div>

            </div>
          </div><!-- End Revenue Card -->

          <!-- Customers Card -->
          <div class="col-xxl-4 col-md-4">

            <div class="card info-card customers-card">

              <div class="card-body">
                <h5 class="card-title">Visiteurs</h5>

                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-people"></i>
                  </div>
                  <div class="ps-3">
                    <h6>{{ $countVisitor }}</h6>
                    {{-- <span class="text-danger small pt-1 fw-bold">12%</span> <span class="text-muted small pt-2 ps-1">decrease</span> --}}

                  </div>
                </div>

              </div>
            </div>

          </div><!-- End Customers Card -->

       

          <div class="col-xxl-12 col-md-12">
               <!-- News & Updates Traffic -->
              <div class="card">
                  <div class="filter">
                  <a class="icon text-bold text-primary" href="{{ route('post') }}">Tous voir<i class="bi bi-arrow-bar-right"></i></a>
                  {{-- <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                      <li class="dropdown-header text-start">
                      <h6>Filter</h6>
                      </li>

                      <li><a class="dropdown-item" href="#">Today</a></li>
                      <li><a class="dropdown-item" href="#">This Month</a></li>
                      <li><a class="dropdown-item" href="#">This Year</a></li>
                  </ul> --}}
                  </div>

                  <div class="card-body pb-0">
                  <h5 class="card-title">Derniers posts</h5>

                  <div class="news">
                    @foreach ($post_recent as $item)
                    <div class="post-item clearfix">
                      <img src="{{ asset($item->getFirstMediaUrl('image')) }}" alt="">
                      <h4><a href="/post/detail?slug={{ $item['slug'] }}">{{ Str::limit($item['title'] ,50)}}</a></h4>         
                          <p><i class="bi bi-eye"> </i> {{ views($item)->count() }} vues &nbsp; &nbsp;   <i class="bi bi-chat-left-quote"> </i> {{ $item->commentaires->count() }}Commentaires
                         &nbsp; &nbsp;<i class="bi bi-calendar-check"></i> Publié {{ \Carbon\Carbon::parse($item['created_at'])->diffForHumans() }}
                          </p>
                      </div>
                    @endforeach
                  </div><!-- End sidebar recent posts-->

                  </div>
              </div><!-- End News & Updates -->

          </div>

        </div>
      </div><!-- End Left side columns -->
    </div>
  </section>
    
@endsection
    
