@extends('site.layout')
@section('title', $post->slug)
@section('description', $post->description)
@section('image', asset($post->getFirstMediaUrl('image')))
@section('url', url()->full())

@section('content')

    <section class="single-post-content py-5">

        <div class="container">
            <div class="row g-4">
                <div class="col-md-9 post-content" data-aos="fade-up">
                    <!-- Breadcrumb -->
                    <nav aria-label="breadcrumb" class="my-4">
                        <ol class="breadcrumb bg-medical-light p-3 rounded shadow-medical">
                            <li class="breadcrumb-item">
                                <a href="javascript:history.go(-1)" class="text-medical-blue text-decoration-none">
                                    <i class="bi bi-arrow-left me-2"></i>Retour
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $post['category']['title'] }}</li>
                        </ol>
                    </nav>


                    <!-- ======= Single Post Content ======= -->
                    <article class="single-post bg-white rounded-3 shadow-medical overflow-hidden">
                        <!-- Image principale -->
                        <div class="position-relative">
                            <img src="{{ asset($post->getFirstMediaUrl('image')) }}" loading="lazy"
                                alt="{{ $post['title'] }}" class="img-fluid w-100"
                                style="max-height:500px; object-fit:cover;">
                            <span class="badge badge-medical position-absolute top-0 start-0 m-4 fs-6">
                                <i
                                    class="bi bi-{{ $post['category']['slug'] == 'actualites' ? 'newspaper' : 'bar-chart-fill' }} me-2"></i>
                                {{ $post['category']['title'] }}
                            </span>
                        </div>

                        <!-- Contenu -->
                        <div class="p-4 p-md-5">
                            <!-- Métadonnées -->
                            <div class="post-meta d-flex flex-wrap gap-4 align-items-center mb-4 pb-4 border-bottom">
                                <span class="text-muted">
                                    <i class="bi bi-calendar3 text-medical-blue me-2"></i>
                                    Publié {{ \Carbon\Carbon::parse($post['created_at'])->diffForHumans() }}
                                </span>
                                <span class="text-muted">
                                    <i class="bi bi-eye-fill text-health-green me-2"></i>
                                    {{ views($post)->count() }} vues
                                </span>
                                <span class="text-muted">
                                    <i class="bi bi-chat-left-quote-fill text-medical-teal me-2"></i>
                                    {{ $post->commentaires->count() }} commentaires
                                </span>
                            </div>

                            <!-- Titre -->
                            <h1 class="mb-4 text-medical-blue fw-bold">{{ $post['title'] }}</h1>

                            <!-- Description -->
                            <div class="post-description" style="line-height: 1.8; font-size: 1.1rem;">
                                {!! $post['description'] !!}
                            </div>

                            <!-- Lien externe -->
                            @if ($post['lien'])
                                <div class="mt-4 p-3 bg-medical-light rounded">
                                    <a href="{{ $post['lien'] }}" target="_blank" class="btn btn-medical">
                                        <i class="bi bi-box-arrow-up-right me-2"></i>
                                        Consulter le site officiel
                                    </a>
                                </div>
                            @endif
                        </div>
                    </article><!-- End Single Post Content -->


                    {{-- Formulaire du sondage --}}
                    @if ($post['category']['title'] == 'Sondage')
                        <div class="row col-12 m-auto mt-5">
                            <!-- Statistiques du sondage -->
                            <div class="card card-medical p-4 mb-4">
                                <div class="card-body">
                                    <h4 class="text-medical-blue fw-bold mb-4 text-center">
                                        <i class="bi bi-bar-chart-fill me-2"></i>
                                        Résultats du Sondage
                                    </h4>

                                    <p class="text-center mb-4 text-muted">
                                        <i class="bi bi-people-fill text-health-green me-2"></i>
                                        <strong>{{ $sondage_total }}</strong> participants
                                    </p>

                                    <div class="statistics-container">
                                        @foreach ($statistic_sondage as $key => $item)
                                            @php
                                                $stat_value = number_format(
                                                    ($item['choice'] * 100) / $sondage_total,
                                                    1,
                                                );
                                                $colors = ['#0066CC', '#00A86B', '#17a2b8', '#8E24AA'];
                                                $color = $colors[$key % count($colors)];
                                            @endphp

                                            <div class="mb-4">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="fw-bold">{{ ++$key }}.
                                                        {{ $item['optionSondage']['title'] }}</span>
                                                    <span class="badge"
                                                        style="background: {{ $color }}">{{ $stat_value }}%</span>
                                                </div>
                                                <div class="progress" style="height: 25px;">
                                                    <div class="progress-bar" role="progressbar"
                                                        style="width:{{ $stat_value }}%; background: {{ $color }};"
                                                        aria-valuenow="{{ $stat_value }}" aria-valuemin="0"
                                                        aria-valuemax="100">
                                                        <strong>{{ $stat_value }}%</strong>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Formulaire de vote -->
                            <div class="card card-medical p-4">
                                <div class="card-body">
                                    <h4 class="text-medical-blue fw-bold mb-4 text-center">
                                        <i class="bi bi-hand-thumbs-up-fill me-2"></i>
                                        Participez au Sondage
                                    </h4>

                                    <form action="{{ route('sondage.store') }}" method="post">
                                        @csrf
                                        <input type="hidden" name="post_id" value="{{ $post['id'] }}">

                                        <p class="text-muted mb-4">Veuillez sélectionner une réponse :</p>

                                        @if ($post['optionSondages'])
                                            @foreach ($post['optionSondages'] as $item)
                                                <div class="form-check mb-3 p-3 rounded section-health-accent">
                                                    <input class="form-check-input" value="{{ $item['id'] }}"
                                                        type="radio" name="sondage_option"
                                                        id="radioExample{{ $item['id'] }}" required />
                                                    <label class="form-check-label ms-2"
                                                        for="radioExample{{ $item['id'] }}"
                                                        style="font-size: 1.1rem; cursor: pointer;">
                                                        {{ $item['title'] }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        @endif

                                        <div class="text-center mt-4">
                                            <button type="submit" class="btn btn-health btn-lg px-5">
                                                <i class="bi bi-send-fill me-2"></i>
                                                Valider ma réponse
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        {{-- End formulaire du sondage --}}
                    @else
                </div>

                <!-- ======= Comments ======= -->
                <div class="comments mt-5">
                    <div class="card card-medical">
                        <div class="card-header bg-medical-light">
                            <h5 class="mb-0 text-medical-blue">
                                <i class="bi bi-chat-left-quote-fill me-2"></i>
                                {{ $post->commentaires->count() }}
                                Commentaire{{ $post->commentaires->count() > 1 ? 's' : '' }}
                            </h5>
                        </div>
                        <div class="card-body">
                            @foreach ($post->commentaires as $item)
                                <div class="comment d-flex mb-4 p-3 rounded section-health-accent">
                                    <div class="flex-shrink-0">
                                        <div class="avatar rounded-circle bg-medical-blue d-flex align-items-center justify-content-center"
                                            style="width: 50px; height: 50px;">
                                            <i class="bi bi-person-fill text-white fs-4"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <div class="comment-meta d-flex align-items-center mb-2">
                                            <h6 class="mb-0 text-medical-blue fw-bold me-2">{{ $item['user_name'] }}</h6>
                                            <span class="text-muted small">
                                                <i class="bi bi-clock me-1"></i>
                                                {{ \Carbon\Carbon::parse($item['created_at'])->diffForHumans() }}
                                            </span>
                                        </div>
                                        <div class="comment-body">
                                            {{ $item['message'] }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div><!-- End Comments -->

                <!-- ======= Comments Form ======= -->
                <div class="row justify-content-center mt-5">
                    <div class="card card-medical">
                        <div class="card-header bg-medical-light">
                            <h5 class="mb-0 text-medical-blue">
                                <i class="bi bi-pencil-square me-2"></i>
                                Laisser un commentaire
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('post.comment') }}" method="POST">
                                @csrf
                                <input type="hidden" name="post_id" value="{{ $post['id'] }}">

                                @guest
                                    <div class="mb-4">
                                        <label for="comment-name" class="form-label text-medical-blue fw-bold">
                                            <i class="bi bi-person-fill me-2"></i>Votre nom
                                        </label>
                                        <input type="text" name="name" class="form-control form-control-lg"
                                            id="comment-name" placeholder="Entrez votre nom" required>
                                    </div>
                                @endguest

                                <div class="mb-4">
                                    <label for="comment-message" class="form-label text-medical-blue fw-bold">
                                        <i class="bi bi-chat-left-text-fill me-2"></i>Votre message
                                    </label>
                                    <textarea class="form-control form-control-lg" id="comment-message" name="message"
                                        placeholder="Partagez votre avis..." required cols="30" rows="6"></textarea>
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="btn btn-medical btn-lg px-5">
                                        <i class="bi bi-send-fill me-2"></i>
                                        Envoyer le commentaire
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div><!-- End Comments Form -->
                @endif
            </div>

            <div class="col-md-3">
                <div class="sidebar-wrapper">
                    @include('site.pages.sections.sidebar')
                </div>
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
