@extends('admin.layout')
@section('title', 'Modifier un article')

@section('content')
<section class="section">
    <form action="{{ route('post.update', $post->id) }}" method="POST" enctype="multipart/form-data"
          data-post-id="{{ $post->id }}">
        @csrf
        <input type="hidden" name="post_id" value="{{ $post->id }}">
        <div class="row g-3">

            {{-- Colonne gauche : titre + image --}}
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="card-title fw-bold mb-3">Informations</h6>

                        <div class="mb-3">
                            <label class="form-label">Titre de l'article <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="title"
                                   value="{{ old('title', $post->title) }}"
                                   class="form-control @error('title') is-invalid @enderror"
                                   required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Image de présentation</label>
                            <input class="form-control" name="image" type="file" accept="image/*">
                            @if ($post->getFirstMediaUrl('image'))
                                <div class="mt-2 d-flex align-items-center gap-2">
                                    <img src="{{ $post->getFirstMediaUrl('image') }}"
                                         alt="Image actuelle"
                                         class="rounded"
                                         style="width:60px; height:60px; object-fit:cover;">
                                    <small class="text-muted">Image actuelle (remplacée si vous en choisissez une nouvelle)</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Colonne droite : catégorie + lien --}}
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="card-title fw-bold mb-3">Paramètres</h6>

                        <div class="mb-3">
                            <label class="form-label">Catégorie <span class="text-danger">*</span></label>
                            <select name="category"
                                    class="form-select @error('category') is-invalid @enderror"
                                    required>
                                <option disabled>— Sélectionner —</option>
                                @foreach ($category as $cat)
                                    <option value="{{ $cat->id }}"
                                            {{ old('category', $post->category_id) == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Lien externe (optionnel)</label>
                            <input type="url"
                                   name="lien"
                                   value="{{ old('lien', $post->lien) }}"
                                   class="form-control @error('lien') is-invalid @enderror"
                                   placeholder="https://exemple.com">
                            @error('lien')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Éditeur TinyMCE --}}
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title fw-bold mb-3">Contenu de l'article</h6>
                        {{-- post_id exposé pour l'upload d'images TinyMCE --}}
                        <script>window.TINYMCE_POST_ID = {{ $post->id }};</script>
                        <textarea name="description" class="tinymce-editor">{{ $post->description }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Bouton valider --}}
            <div class="col-12">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-lg me-2"></i>Enregistrer les modifications
                    </button>
                    <a href="{{ route('post') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Annuler
                    </a>
                </div>
            </div>

        </div>
    </form>
</section>
@endsection
