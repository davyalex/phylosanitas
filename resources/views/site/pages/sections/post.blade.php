{{-- Accueil : articles récents (hors « À la une ») + newsletter + sidebar --}}
<section class="section" aria-labelledby="recent-title">
    <div class="container-xl">
        <div class="layout">

            <div class="layout__main">
                @if($gridPosts->count())
                    <div class="sec-head">
                        <h2 id="recent-title" class="sec-head__title">Articles récents</h2>
                        <a href="{{ route('post.list') }}" class="sec-head__link">Voir tout <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                    </div>

                    <div class="card-grid">
                        @foreach($gridPosts as $item)
                            <x-post-card :post="$item" />
                        @endforeach
                    </div>

                    <div class="more-row">
                        <a href="{{ route('post.list') }}" class="btn btn-outline-medical">Voir tous les articles <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                    </div>
                @elseif(($post ?? collect())->isEmpty())
                    <div class="empty">
                        <i class="bi bi-journal-x" aria-hidden="true"></i>
                        <h2 id="recent-title">Aucun article pour le moment</h2>
                        <p>Les premières publications arrivent bientôt.</p>
                    </div>
                @endif

                {{-- Newsletter + contact --}}
                <div class="cta-band">
                    <div class="cta-band__news">
                        <span class="cta-band__icon" aria-hidden="true"><i class="bi bi-envelope-heart"></i></span>
                        <div>
                            <h2 class="cta-band__title">Restez informé</h2>
                            <p class="cta-band__text">Recevez les dernières actualités santé dans votre boîte mail.</p>
                            <form action="{{ route('newsletter.store') }}" method="POST" class="inline-form">
                                @csrf
                                <label for="nl-email" class="visually-hidden">Votre adresse email</label>
                                <input id="nl-email" type="email" name="email" placeholder="Votre adresse email" required autocomplete="email">
                                <button type="submit" class="btn btn-medical">S'abonner</button>
                            </form>
                        </div>
                    </div>
                    <div class="cta-band__help">
                        <h2 class="cta-band__title">Besoin d'aide&nbsp;?</h2>
                        <p class="cta-band__text">Notre équipe médicale répond à toutes vos questions.</p>
                        <a href="{{ route('contact') }}" class="btn btn-outline-medical">Nous contacter</a>
                    </div>
                </div>
            </div>

            <aside class="layout__side" aria-label="Compléments">
                @include('site.pages.sections.sidebar', ['showRecent' => false])
            </aside>

        </div>
    </div>
</section>
