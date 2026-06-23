{{-- Section articles récents + sidebar --}}
<section class="articles-section py-5">
    <div class="container-xl" data-aos="fade-up">
        <div class="row g-4">

            {{-- Colonne principale --}}
            <div class="col-lg-9">

                <div class="section-header">
                    <h2 class="section-title">
                        <span class="section-accent"></span>Articles récents
                    </h2>
                    <a href="{{ route('post.list') }}" class="section-link">
                        Voir tout <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>

                {{-- Grille articles --}}
                <div class="row g-4">
                    @forelse($post as $item)
                        <div class="col-md-6 col-lg-4">
                            <article class="article-card h-100">
                                <a href="{{ route('post.detail', ['slug' => $item->slug]) }}"
                                   class="article-card-img-link">
                                    <img src="{{ $item->getFirstMediaUrl('image') ?: asset('assets_site/img/medc.jpg') }}"
                                         alt="{{ $item->title }}"
                                         loading="lazy"
                                         class="article-card-img">
                                    <span class="article-card-cat">{{ $item->category->title ?? '' }}</span>
                                </a>
                                <div class="article-card-body">
                                    <h3 class="article-card-title">
                                        <a href="{{ route('post.detail', ['slug' => $item->slug]) }}"
                                           class="text-decoration-none text-dark">
                                            {{ Str::limit($item->title ?? strip_tags($item->description), 65) }}
                                        </a>
                                    </h3>
                                    <div class="article-card-meta">
                                        <span><i class="bi bi-calendar3"></i> {{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}</span>
                                        <span><i class="bi bi-chat-left-quote"></i> {{ $item->commentaires_count ?? 0 }}</span>
                                    </div>
                                </div>
                            </article>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="empty-state text-center py-5">
                                <i class="bi bi-journal-x empty-state-icon"></i>
                                <p class="mt-3 text-muted">Aucun article disponible pour le moment.</p>
                                <a href="{{ route('accueil') }}" class="btn btn-medical mt-2">Retour à l'accueil</a>
                            </div>
                        </div>
                    @endforelse
                </div>

                {{-- Bande catégories --}}
                @if(isset($category) && $category->count())
                <div class="categories-block mt-5">
                    <div class="section-header">
                        <h2 class="section-title">
                            <span class="section-accent"></span>Nos thématiques santé
                        </h2>
                    </div>
                    <div class="row g-3">
                        @foreach($category->filter(fn($c) => !in_array(strtolower($c->slug), ['sondage', 'actualites']))->take(6) as $cat)
                            @php
                                $t = strtolower($cat->title);
                                $catColors = ['#0066CC','#00A86B','#17a2b8','#8E24AA','#E53935','#FF6B35'];
                                $color = $catColors[$loop->index % count($catColors)];
                                $icon = match(true) {
                                    str_contains($t, 'public')  => 'shield-plus-fill',
                                    str_contains($t, 'obés') || str_contains($t,'obes') => 'person-arms-up',
                                    str_contains($t, 'thès') || str_contains($t,'thes') => 'journal-medical',
                                    str_contains($t, 'scolaire') => 'mortarboard-fill',
                                    default => 'file-medical-fill',
                                };
                            @endphp
                            <div class="col-6 col-md-4">
                                <a href="{{ route('post.list', ['category' => $cat->slug]) }}"
                                   class="cat-block-card text-decoration-none">
                                    <div class="cat-block-icon" style="background: {{ $color }}20; color: {{ $color }};">
                                        <i class="bi bi-{{ $icon }}"></i>
                                    </div>
                                    <div class="cat-block-body">
                                        <span class="cat-block-name">{{ $cat->title }}</span>
                                        <span class="cat-block-count">{{ $cat->posts_count ?? $cat->posts->count() }} articles</span>
                                    </div>
                                    <i class="bi bi-arrow-right-circle cat-block-arrow" style="color: {{ $color }};"></i>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- CTA Newsletter + Contact --}}
                <div class="row g-4 mt-2">
                    <div class="col-lg-6">
                        <div class="cta-card cta-card--blue">
                            <div class="cta-icon"><i class="bi bi-envelope-heart-fill"></i></div>
                            <h4 class="cta-title">Restez informé</h4>
                            <p class="cta-text">Recevez les dernières actualités santé directement dans votre boîte mail.</p>
                            <form action="{{ route('newsletter.store') }}" method="POST">
                                @csrf
                                <div class="cta-input-group">
                                    <input type="email" name="email" placeholder="Votre adresse email" required>
                                    <button type="submit"><i class="bi bi-send-fill"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="cta-card cta-card--green">
                            <div class="cta-icon"><i class="bi bi-chat-dots-fill"></i></div>
                            <h4 class="cta-title">Besoin d'aide ?</h4>
                            <p class="cta-text">Notre équipe médicale répond à toutes vos questions.</p>
                            <a href="{{ route('contact') }}" class="cta-btn">
                                <i class="bi bi-envelope-fill me-2"></i>Nous contacter
                            </a>
                            <div class="cta-socials mt-3">
                                <a href="#"><i class="bi bi-facebook"></i></a>
                                <a href="#"><i class="bi bi-twitter"></i></a>
                                <a href="#"><i class="bi bi-instagram"></i></a>
                                <a href="#"><i class="bi bi-linkedin"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                @include('site.pages.sections.partners')

            </div>{{-- /col-lg-9 --}}

            {{-- Sidebar --}}
            <div class="col-lg-3">
                <div class="sidebar-wrapper sticky-sidebar">
                    @include('site.pages.sections.sidebar')
                </div>
            </div>

        </div>
    </div>
</section>
