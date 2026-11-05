@extends('site.layout')
@section('title', 'Le médecin répond à vos questions')
@section('description', 'Posez gratuitement votre question santé : un professionnel de santé vous répond. Questions et réponses publiées anonymement.')
@if(request('q') || $questions->currentPage() > 1)
    @section('robots', 'noindex, follow')
@endif

@section('content')
<x-svc-hero icon="chat-heart" titre="Le médecin répond">
    Posez votre question santé : un professionnel de santé y répond. Les réponses sont publiées sans votre nom, pour que tout le monde en profite.
</x-svc-hero>

<section class="svc-body">
    <div class="container-xl">
        <div class="qa-layout">
            <div>
                @if(session('question_envoyee'))
                    <div class="alert-simple alert-simple--success" role="status">
                        <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                        <span><strong>Merci, votre question a bien été envoyée.</strong> Si elle est retenue, la réponse sera publiée sur cette page, sans votre nom. Pour une situation urgente, n'attendez pas : consultez ou appelez le {{ $site['urgence'] }}.</span>
                    </div>
                @endif

                <form method="GET" action="{{ route('questions.index') }}" class="search-field mb-4" role="search">
                    <i class="bi bi-search" aria-hidden="true"></i>
                    <label for="qa-q" class="visually-hidden">Rechercher une question</label>
                    <input id="qa-q" type="search" name="q" value="{{ request('q') }}" placeholder="Rechercher parmi les réponses…" minlength="2">
                    <button type="submit" class="btn btn-medical">Rechercher</button>
                </form>

                @if($questions->isEmpty())
                    <div class="empty">
                        <i class="bi bi-chat-heart" aria-hidden="true"></i>
                        @if(request('q'))
                            <h2>Aucune réponse ne correspond</h2>
                            <p>Posez votre question : elle aidera peut-être d'autres lecteurs.</p>
                            <a href="{{ route('questions.index') }}" class="btn btn-outline-medical">Voir toutes les réponses</a>
                        @else
                            <h2>Soyez le premier à poser votre question</h2>
                            <p>Les premières réponses de notre équipe seront publiées ici.</p>
                        @endif
                    </div>
                @else
                    <div class="qa-list">
                        @foreach($questions as $q)
                            <article class="qa-item">
                                <div class="qa-item__q">
                                    <h2><a href="{{ route('questions.show', $q->slug) }}">{{ $q->titre_affiche }}</a></h2>
                                </div>
                                <p class="qa-item__a">{{ Str::limit(trim(preg_replace('/\s+/u', ' ', $q->reponse)), 260) }}</p>
                                <div class="meta">
                                    @if($q->category)<span><x-cat-icon :cat="$q->category" />{{ $q->category->title }}</span>@endif
                                    <time datetime="{{ $q->repondu_at?->toDateString() }}"><i class="bi bi-calendar3" aria-hidden="true"></i>{{ $q->repondu_at?->translatedFormat('d F Y') }}</time>
                                    @if($q->medecin && $q->medecin->profil_public)<span><i class="bi bi-patch-check" aria-hidden="true"></i>{{ $q->medecin->nom_public }}</span>@endif
                                </div>
                            </article>
                            @if($loop->iteration === 4 && $questions->count() > 5)
                                <x-ad-slot format="infeed" />
                            @endif
                        @endforeach
                    </div>
                    <div class="pager-wrap">{!! $questions->links('vendor.pagination.custom') !!}</div>
                @endif
            </div>

            <aside class="qa-layout__side" id="poser">
                <div class="ask">
                    <h2 class="ask__title"><i class="bi bi-pencil-square" aria-hidden="true"></i>Poser ma question</h2>
                    <p class="ask__lead">Gratuit et anonyme. Votre nom et votre email ne sont jamais publiés.</p>

                    @if($errors->any())
                        <div class="alert-box" role="alert">
                            <i class="bi bi-exclamation-triangle" aria-hidden="true"></i>
                            <div>@foreach($errors->all() as $e)<span>{{ $e }}</span>@endforeach</div>
                        </div>
                    @endif

                    <form action="{{ route('questions.store') }}" method="POST" id="ask-form">
                        @csrf
                        <x-honeypot />
                        <div class="field">
                            <label for="ask-q">Votre question <span class="req" aria-hidden="true">*</span></label>
                            <textarea id="ask-q" name="question" maxlength="2000" minlength="20" required class="@error('question') is-invalid @enderror"
                                      placeholder="Décrivez votre situation : symptômes, depuis quand, âge, traitements en cours…">{{ old('question') }}</textarea>
                        </div>
                        <div class="field-row">
                            <div class="field">
                                <label for="ask-nom">Prénom ou pseudo <span class="req" aria-hidden="true">*</span></label>
                                <input id="ask-nom" type="text" name="nom" value="{{ old('nom') }}" maxlength="100" required autocomplete="given-name" class="@error('nom') is-invalid @enderror">
                            </div>
                            <div class="field">
                                <label for="ask-age">Tranche d'âge</label>
                                <select id="ask-age" name="tranche_age">
                                    <option value="">—</option>
                                    @foreach(\App\Models\QuestionLecteur::TRANCHES_AGE as $t)
                                        <option value="{{ $t }}" {{ old('tranche_age') === $t ? 'selected' : '' }}>{{ $t }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="field">
                            <label for="ask-email">Email <span class="muted fw-normal">(facultatif)</span></label>
                            <input id="ask-email" type="email" name="email" value="{{ old('email') }}" maxlength="150" autocomplete="email" class="@error('email') is-invalid @enderror">
                            <p class="field__hint">Seulement si l'équipe a besoin d'une précision. Jamais publié.</p>
                        </div>
                        <label class="ask__check">
                            <input type="checkbox" name="accord" value="1" required {{ old('accord') ? 'checked' : '' }}>
                            <span>J'accepte que ma question soit publiée <strong>sans mon nom</strong> avec la réponse, pour aider d'autres lecteurs.</span>
                        </label>
                        <button type="submit" class="btn btn-medical btn-block" id="ask-submit"><i class="bi bi-send" aria-hidden="true"></i> Envoyer ma question</button>
                        <p class="ask__urgent"><i class="bi bi-exclamation-octagon-fill" aria-hidden="true"></i>
                            <span>Ce service ne traite pas les urgences et ne remplace pas une consultation. En cas d'urgence, appelez le <a href="{{ $site['urgence_href'] }}"><strong>{{ $site['urgence'] }}</strong></a>.</span></p>
                        <p class="form-note">Voir notre <a href="{{ route('privacy') }}">politique de confidentialité</a>.</p>
                    </form>
                </div>
            </aside>
        </div>
    </div>
</section>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if($errors->any())
            document.getElementById('poser').scrollIntoView({ behavior: 'smooth', block: 'start' });
        @endif
        var f = document.getElementById('ask-form');
        if (f) f.addEventListener('submit', function () {
            var b = document.getElementById('ask-submit');
            b.disabled = true;
            b.innerHTML = '<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Envoi…';
        });
    });
</script>
@endpush
@endsection
