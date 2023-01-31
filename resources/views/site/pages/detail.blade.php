@extends('site.layout')
@section('title',$post ->title)
@section('description',$post ->description)
@section('image',asset($post->getFirstMediaUrl('image')))
@section('url',url()->current())

@section('content')
<section class="single-post-content">
    <div class="container">
      <div class="row">
        <div class="col-md-9 post-content" data-aos="fade-up">
          <h3 class="category-title"><i class="bi bi-arrow-left"></i><a href="javascript:history.go(-1)">Retour</a>
            </h3>


          <!-- ======= Single Post Content ======= -->
          <div class="single-post">
            <div class="post-meta"><span class="date">{{ $post['category']['title'] }}</span> <span class="mx-1">&bullet;</span> <span>publié {{ \Carbon\Carbon::parse($post['created_at'])->diffForHumans() }}</span> </div>            
           
              <img src="{{ asset($post->getFirstMediaUrl('image'))}}" alt="" class="img-fluid"style="max-width:100; height:auto; object-fit:cover"> 
            <h1 class="mb-5 text-center" style="color: #00456f">{{ $post['title'] }}</h1>
            <p>
                {!! $post['description'] !!}
            </p>

          
          </div><!-- End Single Post Content -->

          <!-- ======= Comments ======= -->
          <div class="comments">
            <h5 class="comment-title py-4">200 Commentaires</h5>
            <div class="comment d-flex mb-4">
              <div class="flex-shrink-0">
                <div class="avatar avatar-sm rounded-circle">
                  <img class="avatar-img" src="{{ asset('assets_admin/img/avatar.jpg') }}" alt="" class="img-fluid">
                </div>
              </div>
              <div class="flex-grow-1 ms-2 ms-sm-3">
                <div class="comment-meta d-flex align-items-baseline">
                  <h6 class="me-2">Jordan Singer</h6>
                  <span class="text-muted">2d</span>
                </div>
                <div class="comment-body">
                  Lorem ipsum, dolor sit amet consectetur adipisicing elit. Non minima ipsum at amet doloremque qui magni, placeat deserunt pariatur itaque laudantium impedit aliquam eligendi repellendus excepturi quibusdam nobis esse accusantium.
                </div>

                <div class="comment-replies bg-light p-3 mt-3 rounded">
                  <h6 class="comment-replies-title mb-4 text-muted text-uppercase">2 replies</h6>

                  <div class="reply d-flex mb-4">
                    <div class="flex-shrink-0">
                      <div class="avatar avatar-sm rounded-circle">
                        <img class="avatar-img" src="{{ asset('assets_admin/img/avatar.jpg') }}" alt="" class="img-fluid">
                    </div>
                    </div>
                    <div class="flex-grow-1 ms-2 ms-sm-3">
                      <div class="reply-meta d-flex align-items-baseline">
                        <h6 class="mb-0 me-2">Brandon Smith</h6>
                        <span class="text-muted">2d</span>
                      </div>
                      <div class="reply-body">
                        Lorem ipsum dolor sit, amet consectetur adipisicing elit.
                      </div>
                    </div>
                  </div>
                  <div class="reply d-flex">
                    <div class="flex-shrink-0">
                      <div class="avatar avatar-sm rounded-circle">
                        <img class="avatar-img" src="{{ asset('assets_admin/img/avatar.jpg') }}" alt="" class="img-fluid">
                    </div>
                    </div>
                    <div class="flex-grow-1 ms-2 ms-sm-3">
                      <div class="reply-meta d-flex align-items-baseline">
                        <h6 class="mb-0 me-2">James Parsons</h6>
                        <span class="text-muted">1d</span>
                      </div>
                      <div class="reply-body">
                        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Distinctio dolore sed eos sapiente, praesentium.
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="comment d-flex">
              <div class="flex-shrink-0">
                <div class="avatar avatar-sm rounded-circle">
                    <img class="avatar-img" src="{{ asset('assets_admin/img/avatar.jpg') }}" alt="" class="img-fluid">
                </div>
              </div>
              <div class="flex-shrink-1 ms-2 ms-sm-3">
                <div class="comment-meta d-flex">
                  <h6 class="me-2">Santiago Roberts</h6>
                  <span class="text-muted">4d</span>
                </div>
                <div class="comment-body">
                  Lorem ipsum dolor sit amet consectetur adipisicing elit. Iusto laborum in corrupti dolorum, quas delectus nobis porro accusantium molestias sequi.
                </div>
              </div>
            </div>
          </div><!-- End Comments -->

          <!-- ======= Comments Form ======= -->
          <div class="row justify-content-center mt-5">

            <div class="col-lg-12">
              <h5 class="comment-title">Laisser un commentaire</h5>
              <div class="row">
                <div class="col-lg-6 mb-3">
                  <label for="comment-name">Nom</label>
                  <input type="text" class="form-control" id="comment-name" placeholder="" required>
                </div>
                <div class="col-lg-6 mb-3">
                  <label for="comment-email">Email</label>
                  <input type="text" class="form-control" id="comment-email" placeholder="" required>
                </div>
                <div class="col-12 mb-3">
                  <label for="comment-message">Message</label>

                  <textarea class="form-control" id="comment-message" placeholder="" required cols="30" rows="10"></textarea>
                </div>
                <div class="col-12">
                  <input type="submit" class="btn btn-primary" value="Envoyer">
                </div>
              </div>
            </div>
          </div><!-- End Comments Form -->

        </div>
        <div class="col-md-3">
                @include('site.pages.sections.sidebar')
        </div>
      </div>
    </div>
  </section>
@endsection



