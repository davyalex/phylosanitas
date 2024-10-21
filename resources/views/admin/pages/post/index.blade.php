@extends('admin.layout')
@section('title', 'Post')

@section('content')
    <style>
        .dropdown-toggle::after {
            content: none;
        }
    </style>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            @if (request('type') == 'sondage')
                                <a href="/admin/post/create?type=sondage" role="button" class="btn btn-primary"> <i
                                        class="bi bi-plus-lg"></i> Ajouter un sondage</a>
                            @else
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('post.create') }}" role="button" class="btn btn-primary"> <i
                                            class="bi bi-plus-lg"></i> Ajouter un article</a>

                                    <!-- ========== Start filtre par category ========== -->

                                    <div class="btn-group text-end">
                                        <button type="button" class="btn btn-primary dropdown-toggle"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-filter"></i> Filtre par categorie
                                        </button>
                                        <ul class="dropdown-menu">
                                            @foreach ($category as $item)
                                                <li><a class="dropdown-item"
                                                        href="{{ route('post', 'category_filter=' . $item['id']) }}">
                                                        {{ $item['title'] }} </a></li>
                                            @endforeach

                                        </ul>
                                    </div>
                                    <!-- ========== End filtre par category ========== -->
                                </div>
                            @endif
                        </h5>

                        @if (request('type') == 'sondage')
                            @include('admin.pages.sondage.index')
                        @else
                            <!-- Table with stripped rows -->
                            <table class="table datatable">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Statut</th>
                                        <th scope="col-2">image</th>
                                        {{-- <th scope="col">title</th> --}}
                                        <th scope="col">categorie</th>
                                        <th scope="col">commentaires</th>
                                        <th scope="col">vues</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($post as $key => $item)
                                        <tr>
                                            <th scope="row">{{ ++$key }}</th>
                                            <td>
                                                <!-- ========== Start mettre le post en privé ou public ========== -->
                                                @if ($item['published'] == 'prive')
                                                    <br><span class=""> <i class="bi bi-circle-fill text-warning"></i>
                                                        non publié</span>
                                                @elseif ($item['published'] == 'public')
                                                    <br> <span class=""> <i
                                                            class="bi bi-circle-fill text-success"></i> en ligne</span>
                                                @endif
                                                <!-- ========== End mettre le post en privé ou public ========== -->




                                                <!-- ========== Start status actualité Une ========== -->
                                                @if ($item->category->slug == 'actualites')
                                                    @if ($item['actualite_une'] == 0)
                                                        <br><span class=""> <i
                                                                class="bi bi-circle-fill text-warning"></i>
                                                            Pas à la Une</span>
                                                    @elseif ($item['actualite_une'] == 1)
                                                        <br> <span class=""> <i
                                                                class="bi bi-circle-fill text-success"></i> A la Une</span>
                                                    @endif
                                                @endif
                                                <!-- ========== End status actualité Une ========== -->

                                            </td>
                                            <td class="col-2"> <img src="{{ $item->getFirstMediaUrl('image') }}"
                                                    alt="{{ $item->getFirstMediaUrl('image') }}"
                                                    style="width: 45px; height: 45px" class="rounded-circle" />

                                            </td>
                                            {{-- <td> {{ Str::limit($item['title'], 20, '...') }}</td> --}}
                                            <td><span class="badge bg-secondary">{{ $item->category->title }}</span></td>
                                            <td>{{ $item->commentaires->count() }}</td>
                                            <td>{{ views($item)->count() }}</td>
                                            <td> {{ \Carbon\Carbon::parse($item['created_at'])->diffForHumans() }}</td>

                                            <td>
                                                <div class="d-flex">

                                                    <div class="dropdown">
                                                        <button role="button"
                                                            class="btn btn-primary rounded-circle dropdown-toggle"
                                                            data-bs-toggle="dropdown" aria-haspopup="false"><i
                                                                class="bi bi-globe"></i></button>
                                                        <div class="dropdown-menu">
                                                            <!-- ========== Start published status ========== -->
                                                            <a href="{{ route('post.published', $item->id) }}"
                                                                class="dropdown-item "
                                                                style="font-weight:700; font-size:1em"><i
                                                                    class="bi bi-lock-fill"></i>
                                                                {{ $item->published == 'public' ? 'Privé' : 'Public' }} </a>
                                                            <!-- ========== End published status ========== -->

                                                            <!-- ========== Start mettre une actualit& à la une ========== -->


                                                            @if ($item->category->slug == 'actualites')
                                                                @if ($item['actualite_une'] == 0)
                                                                    <a href="/admin/post/actualite?actualite_une=1&actualite={{ $item['id'] }}"
                                                                        class="dropdown-item "
                                                                        style="font-weight:700; font-size:1em"><i
                                                                            class="bi bi-image"></i> Mettre à la une</a>
                                                                @else
                                                                    <div class="dropdown-divider"></div>

                                                                    <a href="/admin/post/actualite?actualite_une=0&actualite={{ $item['id'] }}"
                                                                        class="dropdown-item "
                                                                        style="font-weight:700; font-size:1em"><i
                                                                            class="bi bi-image"></i> Retirer de la une</a>
                                                                @endif
                                                            @endif



                                                            <!-- ========== End mettre une actualit& à la une ========== -->

                                                        </div>
                                                    </div>
                                                    <a href="/post/detail?slug={{ $item['slug'] }}" role="button"
                                                        class="btn btn-warning rounded-circle"><i class="bi bi-eye"></i></a>
                                                    <a href="{{ route('post.edit', $item['slug']) }}"
                                                        class="btn btn-success rounded-circle" role="button"
                                                        class="btn btn-success mx-2 "><i class="bi bi-pencil"></i></a>

                                                    <form action="{{ route('post.delete', $item['id']) }}" method="POST">
                                                        @csrf
                                                        <a class="btn btn-danger rounded-circle" data-bs-toggle="modal"
                                                            data-bs-target="#confirmDelete{{ $item->id }}"><i
                                                                class="bi bi-trash"></i> </a>
                                                        @include('admin.partials.deleteConfirm')
                                                    </form>
                                                </div>

                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <!-- End Table with stripped rows -->
                        @endif


                    </div>
                </div>

            </div>
        </div>
    </section>


@endsection
