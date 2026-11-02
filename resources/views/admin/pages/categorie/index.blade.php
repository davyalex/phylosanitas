@extends('admin.layout')
@section('title', 'Catégories')

@section('content')
<section class="section">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-collection-fill me-2 text-primary"></i>Catégories</h4>
            <p class="text-muted small mb-0">{{ $category->count() }} catégorie(s) au total</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddCategory">
            <i class="bi bi-plus-lg me-1"></i>Nouvelle catégorie
        </button>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover datatable align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">#</th>
                        <th>Catégorie</th>
                        <th class="text-center">Articles</th>
                        <th class="text-center">Statut</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($category as $key => $item)
                        <tr>
                            <td class="ps-3 text-muted">{{ ++$key }}</td>

                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="cat-dot" style="background: {{ ['#0066CC','#00A86B','#17a2b8','#6f42c1','#fd7e14','#e83e8c'][$key % 6] }};"></div>
                                    <div>
                                        <span class="fw-semibold">{{ $item->title }}</span>
                                        <br>
                                        <small class="text-muted">{{ $item->slug }}</small>
                                    </div>
                                </div>
                            </td>

                            <td class="text-center">
                                <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary px-3">
                                    {{ $item->posts_count }} article(s)
                                </span>
                            </td>

                            <td class="text-center">
                                @if(in_array(strtolower($item->slug), ['sondage', 'actualites']))
                                    <span class="badge bg-secondary">Système</span>
                                @else
                                    <span class="badge bg-success">Active</span>
                                @endif
                            </td>

                            <td class="text-end pe-3">
                                <div class="d-flex gap-1 justify-content-end">
                                    <a href="{{ route('post.list', ['category' => $item->slug]) }}"
                                       target="_blank"
                                       class="btn btn-sm btn-outline-secondary"
                                       title="Voir sur le site">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </a>

                                    <button class="btn btn-sm btn-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEdit{{ $item->slug }}"
                                            title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    @if($item->posts_count === 0)
                                        <form action="{{ route('category.delete', $item->id) }}" method="POST">
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
                                    @else
                                        <button class="btn btn-sm btn-outline-danger" disabled
                                                title="Impossible de supprimer : contient {{ $item->posts_count }} article(s)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        {{-- Modal édition --}}
                        <div class="modal fade" id="modalEdit{{ $item->slug }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Modifier la catégorie</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" action="{{ route('category.update', $item->slug) }}">
                                        @csrf
                                        <div class="modal-body">
                                            <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                                            <input type="text"
                                                   name="title"
                                                   value="{{ old('title', $item->title) }}"
                                                   class="form-control form-control-lg @error('title') is-invalid @enderror"
                                                   required>
                                            @error('title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted mt-1 d-block">Le slug sera mis à jour automatiquement.</small>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-check-lg me-1"></i>Enregistrer
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <i class="bi bi-collection fs-2 d-block mb-2 opacity-25"></i>
                                Aucune catégorie trouvée.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</section>

{{-- Modal ajout --}}
<div class="modal fade" id="modalAddCategory" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Nouvelle catégorie</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('category.store') }}" novalidate>
                @csrf
                <div class="modal-body">
                    <label class="form-label fw-semibold">Nom de la catégorie <span class="text-danger">*</span></label>
                    <input type="text"
                           name="title"
                           value="{{ old('title') }}"
                           class="form-control form-control-lg @error('title') is-invalid @enderror"
                           placeholder="Ex : Nutrition, Oncologie..."
                           autofocus
                           required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted mt-1 d-block">Le slug est généré automatiquement depuis le nom.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i>Créer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.cat-dot { width:10px; height:10px; border-radius:50%; flex-shrink:0; }
</style>

<script>
    @if($errors->any())
        new bootstrap.Modal(document.getElementById('modalAddCategory')).show();
    @endif
</script>
@endsection
