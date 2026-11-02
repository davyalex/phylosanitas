<div class="table-responsive">
    <table class="table table-hover datatable align-middle">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Image</th>
                <th>Question</th>
                <th>Catégorie</th>
                <th>Statut</th>
                <th class="text-center">Vues</th>
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
                        <a href="{{ route('post.show', $item->id) }}" class="fw-semibold text-decoration-none text-reset" title="Voir le détail">
                            {{ Str::limit(strip_tags($item->description), 60, '…') }}
                        </a>
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

                    <td class="text-center">
                        <span class="views-pill"><i class="bi bi-eye" aria-hidden="true"></i>{{ number_format($item->views_count ?? 0, 0, ',', ' ') }}</span>
                    </td>

                    <td class="text-muted small">
                        {{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}
                    </td>

                    <td>
                        @include('admin.pages.post._actions', ['item' => $item, 'isPoll' => true])
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                        <i class="bi bi-bar-chart fs-3 d-block mb-2"></i>
                        Aucun sondage trouvé.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
