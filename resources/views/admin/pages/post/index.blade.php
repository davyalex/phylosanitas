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

                    {{-- À la une de l'accueil (articles uniquement) --}}
                    @if (request('type') != 'sondage' && isset($une))
                        @include('admin.pages.post._une', ['une' => $une, 'complement' => $complement ?? collect()])
                    @endif

                    {{-- Contenu --}}
                    @if (request('type') == 'sondage')
                        @include('admin.pages.sondage.index')
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover datatable align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="table-admin-hide">#</th>
                                        <th>Image</th>
                                        <th>Titre</th>
                                        <th class="table-admin-hide">Catégorie</th>
                                        <th>Statut</th>
                                        <th class="text-center">Vues</th>
                                        <th class="table-admin-hide">Commentaires</th>
                                        <th class="table-admin-hide">Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($post as $key => $item)
                                        <tr>
                                            <td class="table-admin-hide">{{ ++$key }}</td>

                                            <td>
                                                <img src="{{ $item->getFirstMediaUrl('image') ?: asset('assets_site/img/medc.jpg') }}"
                                                     alt="{{ $item->title }}"
                                                     class="rounded-circle"
                                                     style="width:40px; height:40px; object-fit:cover;">
                                            </td>

                                            <td>
                                                <a href="{{ route('post.show', $item->id) }}" class="fw-semibold d-block text-decoration-none text-reset" style="max-width:240px;" title="Voir le détail">{{ Str::limit($item->title, 55, '…') }}</a>
                                                <span class="badge bg-secondary d-md-none mt-1">{{ $item->category->title }}</span>
                                            </td>

                                            <td class="table-admin-hide">
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
                                                @if ($item->actualite_une)
                                                    <br>
                                                    <span class="badge bg-info mt-1">
                                                        <i class="bi bi-star-fill me-1"></i>À la une
                                                    </span>
                                                @endif
                                            </td>

                                            <td class="text-center" data-sort="{{ (int) ($item->views_count ?? 0) }}">
                                                <span class="views-pill" title="{{ number_format($item->views_count ?? 0, 0, ',', ' ') }} vue{{ ($item->views_count ?? 0) > 1 ? 's' : '' }}">
                                                    <i class="bi bi-eye" aria-hidden="true"></i>{{ number_format($item->views_count ?? 0, 0, ',', ' ') }}
                                                </span>
                                            </td>

                                            <td class="text-center table-admin-hide">
                                                <span class="badge bg-light text-dark">
                                                    <i class="bi bi-chat-left-quote text-muted me-1"></i>
                                                    {{ $item->commentaires_count }}
                                                </span>
                                            </td>

                                            <td class="text-muted small table-admin-hide">
                                                {{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}
                                            </td>

                                            <td>
                                                @include('admin.pages.post._actions', ['item' => $item, 'isPoll' => false])
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center text-muted py-4">
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
