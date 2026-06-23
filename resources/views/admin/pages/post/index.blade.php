@extends('admin.layout')
@section('title', request('type') == 'sondage' ? 'Sondages' : 'Articles')

@section('content')
<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">

                    {{-- Barre d'actions --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        @if (request('type') == 'sondage')
                            <h5 class="card-title mb-0">
                                <i class="bi bi-bar-chart-fill me-2"></i>Sondages
                            </h5>
                            <a href="{{ route('post.create', ['type' => 'sondage']) }}" class="btn btn-primary">
                                <i class="bi bi-plus-lg"></i> Nouveau sondage
                            </a>
                        @else
                            <h5 class="card-title mb-0">
                                <i class="bi bi-card-text me-2"></i>Articles
                            </h5>
                            <div class="d-flex gap-2">
                                <a href="{{ route('post.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-lg"></i> Nouvel article
                                </a>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="bi bi-filter me-1"></i>Filtrer
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item {{ !request('category_filter') ? 'active' : '' }}"
                                               href="{{ route('post') }}">
                                                Tous les articles
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        @foreach ($category as $cat)
                                            <li>
                                                <a class="dropdown-item {{ request('category_filter') == $cat->id ? 'active' : '' }}"
                                                   href="{{ route('post', ['category_filter' => $cat->id]) }}">
                                                    {{ $cat->title }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Contenu --}}
                    @if (request('type') == 'sondage')
                        @include('admin.pages.sondage.index')
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover datatable align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Image</th>
                                        <th>Titre</th>
                                        <th>Catégorie</th>
                                        <th>Statut</th>
                                        <th>Commentaires</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($post as $key => $item)
                                        <tr>
                                            <td>{{ ++$key }}</td>

                                            <td>
                                                <img src="{{ $item->getFirstMediaUrl('image') ?: asset('assets_site/img/medc.jpg') }}"
                                                     alt="{{ $item->title }}"
                                                     class="rounded-circle"
                                                     style="width:45px; height:45px; object-fit:cover;">
                                            </td>

                                            <td>
                                                <span class="fw-semibold">{{ Str::limit($item->title, 50, '…') }}</span>
                                            </td>

                                            <td>
                                                <span class="badge bg-secondary">{{ $item->category->title }}</span>
                                            </td>

                                            <td>
                                                @if ($item->published === 'public')
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-circle-fill me-1" style="font-size:.5rem;"></i>En ligne
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="bi bi-circle-fill me-1" style="font-size:.5rem;"></i>Brouillon
                                                    </span>
                                                @endif
                                                @if ($item->category->slug === 'actualites')
                                                    <br>
                                                    @if ($item->actualite_une)
                                                        <span class="badge bg-info mt-1">
                                                            <i class="bi bi-star-fill me-1"></i>À la une
                                                        </span>
                                                    @else
                                                        <span class="badge bg-light text-muted mt-1">
                                                            <i class="bi bi-star me-1"></i>Pas à la une
                                                        </span>
                                                    @endif
                                                @endif
                                            </td>

                                            <td class="text-center">
                                                <span class="badge bg-light text-dark">
                                                    <i class="bi bi-chat-left-quote text-muted me-1"></i>
                                                    {{ $item->commentaires->count() }}
                                                </span>
                                            </td>

                                            <td class="text-muted small">
                                                {{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}
                                            </td>

                                            <td>
                                                <div class="d-flex gap-1 flex-nowrap">
                                                    {{-- Publier / Dépublier --}}
                                                    <a href="{{ route('post.published', $item->id) }}"
                                                       class="btn btn-sm {{ $item->published === 'public' ? 'btn-warning' : 'btn-success' }}"
                                                       title="{{ $item->published === 'public' ? 'Mettre en brouillon' : 'Publier' }}">
                                                        <i class="bi bi-{{ $item->published === 'public' ? 'eye-slash' : 'eye' }}"></i>
                                                    </a>

                                                    {{-- Mettre à la une (actualités) --}}
                                                    @if ($item->category->slug === 'actualites')
                                                        <a href="/admin/post/actualite?actualite_une={{ $item->actualite_une ? 0 : 1 }}&actualite={{ $item->id }}"
                                                           class="btn btn-sm {{ $item->actualite_une ? 'btn-info' : 'btn-outline-info' }}"
                                                           title="{{ $item->actualite_une ? 'Retirer de la une' : 'Mettre à la une' }}">
                                                            <i class="bi bi-star{{ $item->actualite_une ? '-fill' : '' }}"></i>
                                                        </a>
                                                    @endif

                                                    {{-- Voir en ligne --}}
                                                    <a href="{{ route('post.detail', ['slug' => $item->slug]) }}"
                                                       target="_blank"
                                                       class="btn btn-sm btn-outline-secondary"
                                                       title="Voir sur le site">
                                                        <i class="bi bi-box-arrow-up-right"></i>
                                                    </a>

                                                    {{-- Modifier --}}
                                                    <a href="{{ route('post.edit', $item->slug) }}"
                                                       class="btn btn-sm btn-primary"
                                                       title="Modifier">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>

                                                    {{-- Supprimer --}}
                                                    <form action="{{ route('post.delete', $item->id) }}" method="POST">
                                                        @csrf
                                                        <button type="button"
                                                                class="btn btn-sm btn-danger"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#confirmDelete{{ $item->id }}"
                                                                title="Supprimer">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                        @include('admin.partials.deleteConfirm')
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-4">
                                                <i class="bi bi-journal-x fs-3 d-block mb-2"></i>
                                                Aucun article trouvé.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</section>
@endsection
