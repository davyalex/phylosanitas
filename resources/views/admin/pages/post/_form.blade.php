{{--
    Formulaire article partagé (création + modification).
    Variables : $post (Post|null), $category (catégories)
--}}
@php
    $editing   = isset($post) && $post?->exists;
    $imgUrl    = $editing ? ($post->getFirstMedia('image')?->hasGeneratedConversion('thumb') ? $post->getFirstMedia('image')->getUrl('thumb') : $post->getFirstMediaUrl('image')) : null;
    $published = old('publier', $editing ? $post->published === 'public' : false);
    $articles  = $category->filter(fn($c) => strtolower($c->title) !== 'sondage');
@endphp

<div class="form-shell">
    {{-- Colonne principale --}}
    <div class="d-flex flex-column gap-3">
        <section class="fsec">
            <div class="mb-3">
                <label for="post-title" class="form-label">Titre de l'article <span class="req">*</span> <span class="field-count" data-count-for="post-title"></span></label>
                <input id="post-title" type="text" name="title" value="{{ old('title', $post->title ?? '') }}"
                       class="form-control form-control-lg @error('title') is-invalid @enderror" data-count="70" required
                       placeholder="Ex. : Diabète de type 2 : 7 signes qui doivent alerter">
                <div class="form-text">Un titre clair de moins de 70 caractères s'affiche en entier sur Google.</div>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            @if($editing)
                <div class="small text-muted mb-3">
                    <i class="bi bi-link-45deg"></i> Adresse :
                    <a href="{{ route('post.detail', ['slug' => $post->slug]) }}" target="_blank" rel="noopener">{{ route('post.detail', ['slug' => $post->slug]) }}</a>
                    <span class="ms-1">(l'adresse ne change pas si vous modifiez le titre, pour garder le référencement)</span>
                </div>
            @endif

            <label class="form-label" for="post-content">Contenu</label>
            <textarea id="post-content" name="description" class="tinymce-editor">{{ old('description', $post->description ?? '') }}</textarea>
        </section>

        {{-- SEO --}}
        <section class="fsec">
            <h2 class="fsec__title"><i class="bi bi-google"></i>Référencement Google</h2>
            <p class="fsec__hint">Optionnel. Sans résumé, Google affiche le début de l'article.</p>
            <label for="post-meta" class="form-label">Résumé pour Google <span class="field-count" data-count-for="post-meta"></span></label>
            <textarea id="post-meta" name="meta_description" rows="2" class="form-control @error('meta_description') is-invalid @enderror" data-count="160" maxlength="170"
                      placeholder="1 à 2 phrases qui donnent envie de lire l'article (environ 150 caractères).">{{ old('meta_description', $post->meta_description ?? '') }}</textarea>
            @error('meta_description')<div class="invalid-feedback">{{ $message }}</div>@enderror

            <div class="serp mt-3" aria-label="Aperçu dans Google">
                <span class="serp__url">{{ parse_url(config('app.url'), PHP_URL_HOST) }} › article › <span id="serp-slug">{{ $editing ? $post->slug : '…' }}</span></span>
                <span class="serp__title" id="serp-title">{{ old('title', $post->title ?? '') ?: 'Titre de l\'article' }} — {{ config('app.name') }}</span>
                <span class="serp__desc" id="serp-desc">{{ old('meta_description', $post->meta_description ?? '') ?: 'Le résumé apparaîtra ici…' }}</span>
            </div>
        </section>
    </div>

    {{-- Colonne latérale --}}
    <aside class="form-side">
        <section class="fsec">
            <h2 class="fsec__title"><i class="bi bi-send"></i>Publication</h2>
            <div class="switch-row">
                <div class="switch-row__text"><strong>Publié sur le site</strong><span>Sinon, l'article reste en brouillon</span></div>
                <input type="hidden" name="publier" value="0">
                <input class="form-check-input" type="checkbox" role="switch" name="publier" value="1" {{ $published ? 'checked' : '' }} aria-label="Publié sur le site">
            </div>
            <div class="switch-row">
                <div class="switch-row__text"><strong>À la une</strong><span>Mis en avant dans le bloc « Actualités »</span></div>
                <input type="hidden" name="actualite_une" value="0">
                <input class="form-check-input" type="checkbox" role="switch" name="actualite_une" value="1" {{ old('actualite_une', $post->actualite_une ?? false) ? 'checked' : '' }} aria-label="À la une">
            </div>
        </section>

        <section class="fsec">
            <h2 class="fsec__title"><i class="bi bi-folder2"></i>Classement</h2>
            <div class="mb-3">
                <label for="post-cat" class="form-label">Catégorie <span class="req">*</span></label>
                <select id="post-cat" name="category" class="form-select @error('category') is-invalid @enderror" required>
                    <option value="" disabled {{ old('category', $post->category_id ?? null) ? '' : 'selected' }}>Choisir…</option>
                    @foreach($articles as $cat)
                        <option value="{{ $cat->id }}" {{ (string) old('category', $post->category_id ?? '') === (string) $cat->id ? 'selected' : '' }}>{{ $cat->title }}</option>
                    @endforeach
                </select>
                @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div>
                <label for="post-lien" class="form-label">Source externe</label>
                <input id="post-lien" type="url" name="lien" value="{{ old('lien', $post->lien ?? '') }}" class="form-control @error('lien') is-invalid @enderror" placeholder="https://…">
                <div class="form-text">Affiche un bouton « Consulter la source officielle ».</div>
                @error('lien')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </section>

        <section class="fsec">
            <h2 class="fsec__title"><i class="bi bi-image"></i>Image de couverture</h2>
            <div class="dropzone {{ $imgUrl ? 'has-image' : '' }} @error('image') border-danger @enderror">
                @if($imgUrl)<img src="{{ $imgUrl }}" alt="Image actuelle" class="dropzone__preview">@endif
                <span class="dropzone__empty d-flex flex-column align-items-center gap-1">
                    <i class="bi bi-cloud-arrow-up"></i>
                    <strong>Glissez une image ou cliquez</strong>
                    <small>Paysage 1600 × 900 px conseillé · 5 Mo max</small>
                </span>
                <span class="dropzone__change">Changer l'image</span>
                <input type="file" name="image" accept="image/*" aria-label="Image de couverture">
            </div>
            @error('image')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            <div class="form-text mt-2">Des versions légères (WebP) sont générées automatiquement pour le site.</div>
        </section>
    </aside>
</div>

<div class="form-footer">
    <a href="{{ route('post') }}" class="btn btn-outline-secondary">Annuler</a>
    <div class="form-footer__end">
        @if($editing)
            <a href="{{ route('post.detail', ['slug' => $post->slug]) }}" target="_blank" rel="noopener" class="btn btn-link"><i class="bi bi-box-arrow-up-right me-1"></i>Voir sur le site</a>
        @endif
        <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-check-lg me-1"></i>{{ $editing ? 'Enregistrer les modifications' : 'Créer l\'article' }}</button>
    </div>
</div>

@push('scripts')
<script>
    (function () {
        var t = document.getElementById('post-title'), m = document.getElementById('post-meta');
        var st = document.getElementById('serp-title'), sd = document.getElementById('serp-desc');
        var site = @json(config('app.name'));
        var sync = function () {
            st.textContent = (t.value.trim() || "Titre de l'article") + ' — ' + site;
            sd.textContent = m.value.trim() || 'Le résumé apparaîtra ici…';
        };
        t && t.addEventListener('input', sync);
        m && m.addEventListener('input', sync);
    })();
</script>
@endpush
