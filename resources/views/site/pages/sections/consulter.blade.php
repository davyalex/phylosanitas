{{-- « Quand consulter ? » : carte sombre placée dans la colonne principale de l'accueil (sous la newsletter) --}}
@php
    $stats = Cache::remember('home_stats', 600, function () {
        try {
            return [
                'articles' => \App\Models\Post::where('published', 'public')->count(),
                'themes'   => \App\Models\Category::has('posts')->count(),
            ];
        } catch (\Throwable) {
            return ['articles' => 0, 'themes' => 0];
        }
    });
    $visits = ($site['show_visits'] ?? false) ? (\App\Http\Middleware\CountSiteVisit::totals()['visites'] ?? 0) : null;
@endphp

<section class="consult consult--inline" aria-labelledby="consult-title">
    <div class="consult__inner">
        <div class="consult__art">
            <x-health-art name="hospital" />
        </div>

        <div class="consult__body">
            <span class="eyebrow eyebrow--light"><i class="bi bi-hospital" aria-hidden="true"></i>Soins &amp; urgences</span>
            <h2 id="consult-title" class="consult__title">Quand faut-il consulter&nbsp;?</h2>

            <ul class="consult__list">
                <li class="consult__item consult__item--urgent">
                    <span class="consult__icon" aria-hidden="true"><i class="bi bi-exclamation-octagon"></i></span>
                    <div>
                        <strong>Urgence</strong>
                        <p>Douleur dans la poitrine, gêne respiratoire, malaise ou perte de connaissance : appelez immédiatement le {{ $site['urgence'] ?? '185' }}.</p>
                    </div>
                </li>
                <li class="consult__item consult__item--soon">
                    <span class="consult__icon" aria-hidden="true"><i class="bi bi-clock-history"></i></span>
                    <div>
                        <strong>Rapidement</strong>
                        <p>Fièvre qui persiste, douleur inhabituelle ou symptômes qui s'aggravent : consultez un médecin sans attendre.</p>
                    </div>
                </li>
                <li class="consult__item consult__item--follow">
                    <span class="consult__icon" aria-hidden="true"><i class="bi bi-calendar2-check"></i></span>
                    <div>
                        <strong>Suivi régulier</strong>
                        <p>Maladie chronique, grossesse, jeune enfant : un suivi régulier évite bien des complications.</p>
                    </div>
                </li>
            </ul>

            <div class="consult__actions">
                <a href="{{ $site['urgence_href'] ?? 'tel:185' }}" class="btn btn-urgent">
                    <i class="bi bi-telephone-fill" aria-hidden="true"></i> Urgence : {{ $site['urgence'] ?? '185' }}
                </a>
                @if(config('sante.modules.questions'))
                    <a href="{{ route('questions.index') }}#poser" class="btn btn-ghost-light">Poser une question <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                @else
                    <a href="{{ route('garde') }}" class="btn btn-ghost-light">Pharmacies de garde <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                @endif
            </div>
        </div>
    </div>

    <div>
        <dl class="stats">
            <div class="stat">
                <dt>Articles publiés</dt>
                <dd><span data-count="{{ $stats['articles'] }}">{{ number_format($stats['articles'], 0, ',', ' ') }}</span></dd>
            </div>
            <div class="stat">
                <dt>Thématiques santé</dt>
                <dd><span data-count="{{ $stats['themes'] }}">{{ $stats['themes'] }}</span></dd>
            </div>
            @if($visits !== null)
                <div class="stat">
                    <dt>Visites</dt>
                    <dd><span data-count="{{ $visits }}">{{ number_format($visits, 0, ',', ' ') }}</span></dd>
                </div>
            @endif
            <div class="stat">
                <dt>Rédaction</dt>
                <dd><i class="bi bi-patch-check-fill" aria-hidden="true"></i> Médecin</dd>
            </div>
        </dl>
    </div>
</section>
