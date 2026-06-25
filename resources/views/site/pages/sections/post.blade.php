{{-- Section articles récents + sidebar --}}
<section class="articles-section py-5">
    <div class="container-xl" data-aos="fade-up">
        <div class="row g-4">

            {{-- Colonne principale --}}
            <div class="col-lg-9">

                <div class="section-header mb-4">
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
                        @php
                            $isSondage = strtolower($item->category->title ?? '') === 'sondage';
                            $rawTitle  = $item->title ?? strip_tags($item->description ?? '');
                            $title     = Str::title(Str::limit($rawTitle, 70));
                            $excerpt   = $isSondage
                                ? Str::limit(strip_tags($item->description ?? ''), 90)
                                : Str::limit(strip_tags($item->description ?? ''), 90);
                            $wordCount = str_word_count(strip_tags($item->description ?? ''));
                            $readMin   = max(1, round($wordCount / 200));
                            $catIcon   = match(true) {
                                str_contains(strtolower($item->category->slug ?? ''), 'actualite') => 'newspaper',
                                str_contains(strtolower($item->category->slug ?? ''), 'sondage')   => 'bar-chart-fill',
                                str_contains(strtolower($item->category->title ?? ''), 'nutri')    => 'egg-fried',
                                str_contains(strtolower($item->category->title ?? ''), 'sport')    => 'heart-pulse-fill',
                                str_contains(strtolower($item->category->title ?? ''), 'obés')     => 'person-arms-up',
                                str_contains(strtolower($item->category->title ?? ''), 'scolaire') => 'mortarboard-fill',
                                default => 'file-medical-fill',
                            };
                        @endphp
                        <div class="col-sm-6 col-lg-4">
                            <article class="pcard h-100">
                                <a href="{{ route('post.detail', ['slug' => $item->slug]) }}" class="pcard__img-link">
                                    <img src="{{ $item->getFirstMediaUrl('image') ?: asset('assets_site/img/medc.jpg') }}"
                                         alt="{{ $rawTitle }}"
                                         loading="lazy"
                                         class="pcard__img">
                                    <span class="pcard__cat">
                                        <i class="bi bi-{{ $catIcon }} me-1"></i>
                                        {{ $item->category->title ?? '' }}
                                    </span>
                                </a>
                                <div class="pcard__body">
                                    <h3 class="pcard__title">
                                        <a href="{{ route('post.detail', ['slug' => $item->slug]) }}">
                                            {{ $title }}
                                        </a>
                                    </h3>
                                    @if($excerpt)
                                        <p class="pcard__excerpt">{{ $excerpt }}</p>
                                    @endif
                                    <div class="pcard__meta">
                                        <span><i class="bi bi-calendar3"></i> {{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}</span>
                                        <span><i class="bi bi-chat-left-quote"></i> {{ $item->commentaires_count ?? 0 }}</span>
                                        <span><i class="bi bi-clock"></i> {{ $readMin }} min</span>
                                    </div>
                                </div>
                            </article>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <i class="bi bi-journal-x text-muted" style="font-size:3rem;"></i>
                            <p class="mt-3 text-muted">Aucun article disponible pour le moment.</p>
                            <a href="{{ route('accueil') }}" class="btn btn-medical mt-2">Retour à l'accueil</a>
                        </div>
                    @endforelse
                </div>

                {{-- Bande catégories --}}
                @if(isset($category) && $category->count())
                <div class="mt-5">
                    <div class="section-header mb-3">
                        <h2 class="section-title">
                            <span class="section-accent"></span>Nos thématiques santé
                        </h2>
                    </div>
                    <div class="row g-3">
                        @foreach($category->filter(fn($c) => !in_array(strtolower($c->slug), ['sondage','actualites']))->take(6) as $cat)
                            @php
                                $t = strtolower($cat->title);
                                $palette = ['#0066CC','#00A86B','#17a2b8','#8E24AA','#E53935','#FF6B35'];
                                $col  = $palette[$loop->index % count($palette)];
                                $ico  = match(true) {
                                    str_contains($t,'public')   => 'shield-plus-fill',
                                    str_contains($t,'obés')     => 'person-arms-up',
                                    str_contains($t,'thès')     => 'journal-medical',
                                    str_contains($t,'scolaire') => 'mortarboard-fill',
                                    str_contains($t,'nutri')    => 'egg-fried',
                                    str_contains($t,'sport')    => 'heart-pulse-fill',
                                    default                     => 'file-medical-fill',
                                };
                            @endphp
                            <div class="col-6 col-md-4">
                                <a href="{{ route('post.list', ['category' => $cat->slug]) }}"
                                   class="cat-pill-card text-decoration-none" style="--cat-color:{{ $col }}">
                                    <span class="cat-pill-card__icon">
                                        <i class="bi bi-{{ $ico }}"></i>
                                    </span>
                                    <span class="cat-pill-card__info">
                                        <strong>{{ Str::title($cat->title) }}</strong>
                                        <small>{{ $cat->posts_count ?? 0 }} articles</small>
                                    </span>
                                    <i class="bi bi-chevron-right cat-pill-card__arrow"></i>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- CTA Newsletter + Contact --}}
                <div class="row g-4 mt-2">
                    <div class="col-12 col-lg-6">
                        <div class="cta-card cta-card--blue">
                            <div class="cta-icon"><i class="bi bi-envelope-heart-fill"></i></div>
                            <h4 class="cta-title">Restez informé</h4>
                            <p class="cta-text">Recevez les dernières actualités santé dans votre boîte mail.</p>
                            <form action="{{ route('newsletter.store') }}" method="POST">
                                @csrf
                                <div class="cta-input-group">
                                    <input type="email" name="email" placeholder="Votre adresse email" required>
                                    <button type="submit"><i class="bi bi-send-fill"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="cta-card cta-card--green">
                            <div class="cta-icon"><i class="bi bi-chat-dots-fill"></i></div>
                            <h4 class="cta-title">Besoin d'aide ?</h4>
                            <p class="cta-text">Notre équipe médicale répond à toutes vos questions.</p>
                            <a href="{{ route('contact') }}" class="cta-btn">
                                <i class="bi bi-envelope-fill me-2"></i>Nous contacter
                            </a>
                            <div class="cta-socials mt-3">
                                <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                                <a href="#" aria-label="Twitter"><i class="bi bi-twitter"></i></a>
                                <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                                <a href="#" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
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
