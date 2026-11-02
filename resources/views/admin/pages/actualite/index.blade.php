@extends('admin.layout')
@section('title', 'Carrousel Hero')

@section('content')
<section class="section">

    {{-- En-tête --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-images me-2 text-primary"></i>Carrousel Hero</h4>
            <p class="text-muted mb-0 small">Gérez les slides affichés en haut de la page d'accueil.</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddSlide">
            <i class="bi bi-plus-lg me-1"></i> Nouveau slide
        </button>
    </div>

    {{-- Conseils --}}
    <div class="alert alert-info d-flex gap-2 align-items-start small mb-4">
        <i class="bi bi-info-circle-fill mt-1 flex-shrink-0"></i>
        <span>
            <strong>Format recommandé :</strong> image paysage 1900 × 750 px · max 5 Mo ·
            Les slides sont affichés dans l'ordre défini. Utilisez ↑ ↓ pour réordonner.
        </span>
    </div>

    {{-- Grille des slides --}}
    @if($actualite->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="bi bi-images" style="font-size:3rem; opacity:.3;"></i>
            <p class="mt-3">Aucun slide pour le moment. Ajoutez votre premier slide.</p>
        </div>
    @else
        <div class="row g-4">
            @foreach($actualite as $item)
                <div class="col-md-6 col-xl-4">
                    <div class="slide-card {{ $item->actif ? '' : 'slide-card--inactive' }}">

                        {{-- Aperçu image --}}
                        <div class="slide-preview">
                            <img src="{{ $item->getFirstMediaUrl('image') ?: asset('assets_site/img/medc.jpg') }}"
                                 alt="{{ $item->title }}"
                                 class="slide-preview-img">
                            <div class="slide-preview-overlay">
                                <p class="slide-preview-title">{{ Str::limit($item->title, 60) }}</p>
                                @if($item->sous_titre)
                                    <p class="slide-preview-sub">{{ Str::limit($item->sous_titre, 80) }}</p>
                                @endif
                            </div>
                            {{-- Badge statut --}}
                            <span class="slide-status-badge {{ $item->actif ? 'slide-status-badge--on' : 'slide-status-badge--off' }}">
                                <i class="bi bi-circle-fill me-1" style="font-size:.45rem;"></i>
                                {{ $item->actif ? 'Actif' : 'Inactif' }}
                            </span>
                            {{-- Badge ordre --}}
                            <span class="slide-order-badge">#{{ $item->ordre }}</span>
                        </div>

                        {{-- Infos --}}
                        <div class="slide-body">
                            <h6 class="slide-title fw-bold mb-1">{{ Str::limit($item->title, 50) }}</h6>
                            @if($item->sous_titre)
                                <p class="slide-subtitle text-muted small mb-1">{{ Str::limit($item->sous_titre, 70) }}</p>
                            @endif
                            @if($item->lien)
                                <a href="{{ $item->lien }}" target="_blank" class="small text-primary text-truncate d-block mb-2">
                                    <i class="bi bi-link-45deg"></i> {{ Str::limit($item->lien, 40) }}
                                </a>
                            @endif
                            <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top">
                                <small class="text-muted">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    {{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}
                                </small>
                                {{-- Boutons ordre --}}
                                <div class="d-flex gap-1">
                                    <a href="{{ route('actualite.up', $item->id) }}"
                                       class="btn btn-sm btn-outline-secondary py-0 px-2"
                                       title="Monter">
                                        <i class="bi bi-arrow-up"></i>
                                    </a>
                                    <a href="{{ route('actualite.down', $item->id) }}"
                                       class="btn btn-sm btn-outline-secondary py-0 px-2"
                                       title="Descendre">
                                        <i class="bi bi-arrow-down"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="slide-actions">
                            {{-- Toggle actif --}}
                            <a href="{{ route('actualite.toggle', $item->id) }}"
                               class="btn btn-sm {{ $item->actif ? 'btn-warning' : 'btn-success' }}"
                               title="{{ $item->actif ? 'Désactiver' : 'Activer' }}">
                                <i class="bi bi-{{ $item->actif ? 'eye-slash' : 'eye' }}"></i>
                                {{ $item->actif ? 'Désactiver' : 'Activer' }}
                            </a>

                            {{-- Modifier --}}
                            <button class="btn btn-sm btn-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEdit{{ $item->id }}"
                                    title="Modifier">
                                <i class="bi bi-pencil"></i> Modifier
                            </button>

                            {{-- Supprimer --}}
                            <form action="{{ route('actualite.delete', $item->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="button"
                                        class="btn btn-sm btn-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#confirmDeleteSlide{{ $item->id }}"
                                        title="Supprimer">
                                    <i class="bi bi-trash"></i>
                                </button>
                                @include('admin.partials.deleteConfirm')
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Modal édition --}}
                <div class="modal fade" id="modalEdit{{ $item->id }}" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <i class="bi bi-pencil me-2"></i>Modifier le slide
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('actualite.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="modal-body">
                                    <div class="row g-3">
                                        {{-- Aperçu actuel --}}
                                        <div class="col-12">
                                            <img src="{{ $item->getFirstMediaUrl('image') ?: asset('assets_site/img/medc.jpg') }}"
                                                 alt="Aperçu actuel"
                                                 class="w-100 rounded-3 mb-3"
                                                 style="height:180px; object-fit:cover;">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Titre <span class="text-danger">*</span></label>
                                            <input type="text" name="title" value="{{ old('title', $item->title) }}"
                                                   class="form-control" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Texte du bouton</label>
                                            <input type="text" name="texte_bouton"
                                                   value="{{ old('texte_bouton', $item->texte_bouton) }}"
                                                   class="form-control"
                                                   placeholder="Lire l'article">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Sous-titre (accroche)</label>
                                            <input type="text" name="sous_titre"
                                                   value="{{ old('sous_titre', $item->sous_titre) }}"
                                                   class="form-control"
                                                   placeholder="Courte description visible sur le slide">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Lien du bouton (URL)</label>
                                            <input type="url" name="lien"
                                                   value="{{ old('lien', $item->lien) }}"
                                                   class="form-control"
                                                   placeholder="https://...">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">
                                                Nouvelle image
                                                <small class="text-muted fw-normal">(laisser vide pour conserver l'actuelle)</small>
                                            </label>
                                            <input type="file" name="image" class="form-control" accept="image/*">
                                        </div>
                                    </div>
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

            @endforeach
        </div>
    @endif

</section>

{{-- Modal ajout nouveau slide --}}
<div class="modal fade" id="modalAddSlide" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle me-2"></i>Nouveau slide
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('actualite.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Titre <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title') }}" required>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Texte du bouton CTA</label>
                            <input type="text" name="texte_bouton" class="form-control"
                                   value="{{ old('texte_bouton', "Lire l'article") }}"
                                   placeholder="Lire l'article">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Sous-titre (accroche)</label>
                            <input type="text" name="sous_titre" class="form-control"
                                   value="{{ old('sous_titre') }}"
                                   placeholder="Texte affiché sous le titre sur le slide">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Lien du bouton (URL)</label>
                            <input type="url" name="lien" class="form-control @error('lien') is-invalid @enderror"
                                   value="{{ old('lien') }}" placeholder="https://...">
                            @error('lien')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                Image du slide <span class="text-danger">*</span>
                                <small class="text-muted fw-normal ms-1">1900 × 750 px recommandé · max 5 Mo</small>
                            </label>
                            <input type="file" name="image" id="imagePreviewInput"
                                   class="form-control @error('image') is-invalid @enderror"
                                   accept="image/*" required>
                            @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <img id="imagePreview" src="#" alt="Aperçu"
                                 class="w-100 rounded-3 mt-2 d-none"
                                 style="height:180px; object-fit:cover;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i>Ajouter le slide
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Styles cards carrousel --}}
<style>
.slide-card {
    background: #fff;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 2px 12px rgba(0,0,0,.08);
    border: 1.5px solid #eef0f3;
    transition: box-shadow .2s, transform .2s;
}
.slide-card:hover { box-shadow: 0 6px 24px rgba(0,0,0,.13); transform: translateY(-3px); }
.slide-card--inactive { opacity: .6; filter: grayscale(.4); }

.slide-preview {
    position: relative;
    height: 180px;
    overflow: hidden;
}
.slide-preview-img {
    width: 100%;
    height: 180px;
    object-fit: cover;
    display: block;
    transition: transform .4s;
}
.slide-card:hover .slide-preview-img { transform: scale(1.04); }
.slide-preview-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(0,0,0,.75) 0%, transparent 60%);
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    padding: 14px;
}
.slide-preview-title {
    color: #fff;
    font-weight: 700;
    font-size: .88rem;
    line-height: 1.3;
    margin: 0 0 4px;
}
.slide-preview-sub {
    color: rgba(255,255,255,.8);
    font-size: .75rem;
    margin: 0;
}

.slide-status-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: .72rem;
    font-weight: 700;
    display: flex;
    align-items: center;
}
.slide-status-badge--on  { background: rgba(25,135,84,.9);  color: #fff; }
.slide-status-badge--off { background: rgba(108,117,125,.85); color: #fff; }

.slide-order-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: rgba(0,0,0,.55);
    color: #fff;
    font-size: .72rem;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 20px;
}

.slide-body  { padding: 14px 16px 10px; }
.slide-title { font-size: .92rem; }
.slide-subtitle { font-size: .78rem; line-height: 1.4; }
.slide-actions {
    padding: 10px 12px 14px;
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}
.slide-actions .btn { flex: 1; min-width: 80px; font-size: .78rem; }
</style>

{{-- Aperçu image dans le modal ajout --}}
<script>
    document.getElementById('imagePreviewInput')?.addEventListener('change', function () {
        var file = this.files[0];
        var preview = document.getElementById('imagePreview');
        if (file) {
            var reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
            };
            reader.readAsDataURL(file);
        } else {
            preview.classList.add('d-none');
        }
    });

    @if($errors->any())
        var addModal = new bootstrap.Modal(document.getElementById('modalAddSlide'));
        addModal.show();
    @endif
</script>
@endsection
