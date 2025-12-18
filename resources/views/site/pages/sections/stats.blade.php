<!-- Section Catégories & Newsletter -->
<section class="categories-newsletter-section py-5" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
    <div class="container">
        <!-- Catégories Populaires -->
        <div class="row mb-5">
            <div class="col-12 text-center mb-4">
                <h2 class="fw-bold text-medical-blue mb-2">
                    <i class="bi bi-collection-fill me-2"></i>
                    Explorez Nos Thématiques Santé
                </h2>
                <p class="text-muted">Découvrez nos articles classés par catégories</p>
            </div>

            @foreach($category->take(6) as $cat)
                <div class="col-lg-4 col-md-6 mb-4">
                    <a href="{{ route('post.list', ['category' => $cat->slug]) }}" class="text-decoration-none">
                        <div class="card card-medical h-100 border-0 category-card">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center">
                                    <div class="category-icon me-3">
                                        @if($cat->title == 'Sondage')
                                            <i class="bi bi-bar-chart-fill"></i>
                                        @elseif(Str::contains(strtolower($cat->title), ['actualité', 'actualite']))
                                            <i class="bi bi-newspaper"></i>
                                        @elseif(Str::contains(strtolower($cat->title), ['nutrition', 'alimentation']))
                                            <i class="bi bi-egg-fried"></i>
                                        @elseif(Str::contains(strtolower($cat->title), ['sport', 'fitness']))
                                            <i class="bi bi-heart-pulse-fill"></i>
                                        @elseif(Str::contains(strtolower($cat->title), ['mental', 'psycho']))
                                            <i class="bi bi-brain"></i>
                                        @elseif(Str::contains(strtolower($cat->title), ['enfant', 'bébé']))
                                            <i class="bi bi-emoji-smile-fill"></i>
                                        @else
                                            <i class="bi bi-file-medical-fill"></i>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="mb-1 fw-bold">{{ $cat->title }}</h5>
                                        <small class="text-muted">
                                            <i class="bi bi-file-earmark-text"></i>
                                            {{ $cat->posts->where('published', 'public')->count() }} articles
                                        </small>
                                    </div>
                                    <div>
                                        <i class="bi bi-arrow-right-circle text-medical-blue" style="font-size: 1.5rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        <!-- Section Newsletter & Contact -->
        <div class="row g-4">
            <!-- Newsletter -->
            <div class="col-lg-6">
                <div class="card card-medical border-0 h-100" style="background: linear-gradient(135deg, var(--medical-blue) 0%, var(--medical-teal) 100%);">
                    <div class="card-body p-5 text-white">
                        <div class="mb-4">
                            <i class="bi bi-envelope-heart-fill" style="font-size: 3rem;"></i>
                        </div>
                        <h3 class="fw-bold mb-3">Restez Informé</h3>
                        <p class="mb-4">
                            Inscrivez-vous à notre newsletter et recevez les dernières actualités santé directement dans votre boîte mail.
                        </p>
                        <form class="newsletter-form">
                            <div class="input-group">
                                <input type="email" class="form-control form-control-lg" placeholder="Votre adresse email" required>
                                <button class="btn btn-light btn-lg" type="submit">
                                    <i class="bi bi-send-fill me-2"></i>S'abonner
                                </button>
                            </div>
                            <small class="d-block mt-2 opacity-75">
                                <i class="bi bi-shield-check me-1"></i>
                                Vos données sont protégées
                            </small>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Contact / CTA -->
            <div class="col-lg-6">
                <div class="card card-medical border-0 h-100" style="background: linear-gradient(135deg, var(--health-green) 0%, var(--health-green-light) 100%);">
                    <div class="card-body p-5 text-white">
                        <div class="mb-4">
                            <i class="bi bi-chat-dots-fill" style="font-size: 3rem;"></i>
                        </div>
                        <h3 class="fw-bold mb-3">Besoin d'Aide ?</h3>
                        <p class="mb-4">
                            Notre équipe est à votre écoute pour répondre à vos questions et vous accompagner dans votre recherche d'informations santé.
                        </p>
                        <div class="d-flex flex-column gap-3">
                            <a href="{{ route('contact') }}" class="btn btn-light btn-lg">
                                <i class="bi bi-envelope-fill me-2"></i>Nous Contacter
                            </a>
                            <div class="d-flex gap-3 mt-2">
                                <a href="#" class="text-white">
                                    <i class="bi bi-facebook" style="font-size: 1.5rem;"></i>
                                </a>
                                <a href="#" class="text-white">
                                    <i class="bi bi-twitter" style="font-size: 1.5rem;"></i>
                                </a>
                                <a href="#" class="text-white">
                                    <i class="bi bi-instagram" style="font-size: 1.5rem;"></i>
                                </a>
                                <a href="#" class="text-white">
                                    <i class="bi bi-linkedin" style="font-size: 1.5rem;"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.category-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
}

.category-icon {
    width: 60px;
    height: 60px;
    border-radius: 15px;
    background: linear-gradient(135deg, var(--medical-blue-light) 0%, var(--medical-teal-light) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    color: white;
}

.category-card:hover .category-icon {
    transform: scale(1.1);
    transition: transform 0.3s ease;
}

.newsletter-form .form-control:focus {
    box-shadow: none;
    border-color: white;
}

.btn-light:hover {
    transform: scale(1.05);
    transition: transform 0.2s ease;
}
</style>

