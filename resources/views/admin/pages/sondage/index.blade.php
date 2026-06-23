<div class="table-responsive">
    <table class="table table-hover datatable align-middle">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Image</th>
                <th>Question</th>
                <th>Catégorie</th>
                <th>Statut</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($sondage as $key => $item)
                <tr>
                    <td>{{ ++$key }}</td>

                    <td>
                        <img src="{{ $item->getFirstMediaUrl('image') ?: asset('assets_site/img/medc.jpg') }}"
                             alt="sondage"
                             class="rounded-circle"
                             style="width:45px; height:45px; object-fit:cover;">
                    </td>

                    <td>
                        <span class="fw-semibold">
                            {{ Str::limit(strip_tags($item->description), 60, '…') }}
                        </span>
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
                    </td>

                    <td class="text-muted small">
                        {{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}
                    </td>

                    <td>
                        <div class="d-flex gap-1 flex-nowrap">
                            {{-- Publier / Dépublier --}}
                            <a href="{{ route('post.published', $item->id) }}"
                               class="btn btn-sm {{ $item->published === 'public' ? 'btn-warning' : 'btn-success' }}"
                               title="{{ $item->published === 'public' ? 'Dépublier' : 'Publier' }}">
                                <i class="bi bi-{{ $item->published === 'public' ? 'eye-slash' : 'eye' }}"></i>
                            </a>

                            {{-- Voir en ligne --}}
                            <a href="{{ route('post.detail', ['slug' => $item->slug]) }}"
                               target="_blank"
                               class="btn btn-sm btn-outline-secondary"
                               title="Voir sur le site">
                                <i class="bi bi-box-arrow-up-right"></i>
                            </a>

                            {{-- Modifier — utilise l'ID (pas le slug) --}}
                            <a href="{{ route('post.edit-sondage', $item->id) }}"
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
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="bi bi-bar-chart fs-3 d-block mb-2"></i>
                        Aucun sondage trouvé.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
