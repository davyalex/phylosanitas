@extends('site.layout')
@section('title',$post->slug)
@section('description',$post->description)
@section('image',asset($post->getFirstMediaUrl('image')))
@section('url',url()->current())
{{-- @section('meta')
<x-meta
title="{{ $post->slug }}"
description="{{ $post->description }}"
image="{{ asset($post->getFirstMediaUrl('image')) }}"
url="{{ url()->current() }}"
/>
@endsection
  --}}
@section('content')

<section class="single-post-content">

 
    <div class="container">
      <div class="row">
        <div class="col-md-9 post-content" data-aos="fade-up">
          <h3 class="category-title"><i class="bi bi-arrow-left"></i><a href="javascript:history.go(-1)">Retour</a>
            </h3>


          <!-- ======= Single Post Content ======= -->
          <div class="single-post">
            <div class="post-meta">
              <span class="date">{{ $post['category']['title'] }}</span> <span class="mx-1">&bullet;</span> <span>publié {{ \Carbon\Carbon::parse($post['created_at'])->diffForHumans() }}</span> 
            </div>            
           
              <img src="{{ asset($post->getFirstMediaUrl('image'))}}" alt="" class="img-fluid"style="width:100%; height:auto; object-fit:cover"> 
            <h1 class="mb-5 text-center" style="color: #00456f">{{ $post['title'] }}</h1>
            <div class="max-width:50%; max-height:200px; object-fit:cover">
              {!!  $post['description'] !!}

            </div>
          

          
          </div><!-- End Single Post Content -->


          {{-- formulaire du sondage --}}
         
              
         
            @if ($post['category']['title']=='Sondage')
            <div class="row col-12 m-auto ">
              <h4 class="fw-bold text-center mt-3"></h4>
               {{-- affichage des statistics du sondage --}}
              <div class="alert alert-info ">
                <p class="mb-1 text-bold text-primary" style="text-align:center; font-size:21px"><i class="bi bi-people"></i> Partcipants: {{ $sondage_total }} </p>
                <div class="d-flex ">
                   @foreach ($statistic_sondage as $item)
                   <span class="m-auto " style="font-weight:400; font-size:19px;">{{ $item['optionSondage']['title'] }} 
                      <i class="text-dark">{{ number_format(($item['choice']*100) / $sondage_total,2) }} %</i>
                   </span><br>
                   @endforeach
                 </div>
              </div>
                 {{-- affichage des statistics du sondage --}}
              <form class="bg-white px-4" action="{{ route('sondage.store') }}" method="post">
                @csrf
                @if ($post['optionSondages'])
                <span class="text-danger">Veuillez sélectionner une réponse</span>
                @foreach ($post['optionSondages'] as $item)
                <div class="form-check mb-2 " style="font-size: 25px;">
                  <input type="text"  name="post_id" value="{{ $post['id'] }}" hidden required>
                  <input class="form-check-input"  value="{{ $item['id'] }}" type="radio" name="sondage_option" id="radioExample{{ $item['id'] }}" required />
                  <label class="form-check-label" style="font-size:15px"  for="radioExample{{ $item['id'] }}">
                    <span style="font-size: 25px">  {{ $item['title'] }}</span>
                  </label>
                </div>
                @endforeach
                    
                @endif
               
                <div class="text-center mt-4">
                  <button type="submit"  class="btn btn-primary m-auto">Valider ma reponse <i class="bi bi-send"></i></button>
                </div>
              </form>
            </div>
            {{-- end-formulaire du sondage --}}

            @else
               <!-- ======= Comments ======= -->
          <div class="comments">
            <h5 class="comment-title py-4">{{ $post->commentaires->count() }} Commentaires</h5>
            @foreach ($post->commentaires as $item)
                
            <div class="comment d-flex mb-3">
              <div class="flex-shrink-0">
                <div class="avatar avatar-sm rounded-circle">
                    <img class="avatar-img" src="{{ asset('assets_admin/img/avatar.jpg') }}" alt="" class="img-fluid">
                </div>
              </div>
              <div class="flex-shrink-1 ms-2 ms-sm-3">
                <div class="comment-meta d-flex">
                  <h6 class="me-2">{{ $item['user_name'] }}</h6>
                  <span class="text-muted">{{ \Carbon\Carbon::parse($item['created_at'])->diffForHumans() }}</span>
                </div>
                <div class="comment-body">
                  {{ $item['message'] }}
                </div>
              </div>
            </div>

            @endforeach

          </div><!-- End Comments -->

          <!-- ======= Comments Form ======= -->
          <div class="row justify-content-center mt-5">

            <form action="{{ route('post.comment') }}" method="POST">
                @csrf
              <div class="col-lg-12">
              <h5 class="comment-title">Laisser un commentaire</h5>
              <div class="row">
                <input type="number" name="post_id" value="{{$post['id']}}" hidden>
                @guest
                <div class="col-lg-6 mb-3">
                  <label for="comment-name">Nom</label>
                  <input type="text" name="name" class="form-control" id="comment-name" placeholder="" required>
                </div>
                @endguest
              
                {{-- <div class="col-lg-6 mb-3">
                  <label for="comment-email">Email</label>
                  <input type="text" name="email" class="form-control" id="comment-email" placeholder="">
                </div> --}}
                <div class="col-12 mb-3">
                  <label for="comment-message">Message</label>

                  <textarea class="form-control" id="comment-message" name="message" placeholder="" required cols="30" rows="10"></textarea>
                </div>
                <div class="col-12">
                  <input type="submit" class="btn btn-primary" value="Envoyer">
                </div>
              </div>
            </div>
            </form>

          
          </div><!-- End Comments Form -->
            @endif



          

       

        </div>
        <div class="col-md-3">
                @include('site.pages.sections.sidebar')
        </div>
      </div>
    </div>
  </section>

  <script>
    // $(document).ready(function(){


    //   $('#btn').click(function (e) { 
    //     e.preventDefault();

    //     var post_sondage = $('#post_sondage').val();
    //     var option_sondage = $('#option_sondage').val();

    //     $.ajax({
    //       type: "POST",
    //       url: "{{ route('sondage.store') }}",
    //       data: { post_sondage:post_sondage, option_sondage:option_sondage },
    //       dataType: "json",
    //       success: function (response) {
            
    //       }
    //     });

        
    //   });
    // })
  </script>
@endsection



