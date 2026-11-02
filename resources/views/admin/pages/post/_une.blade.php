{{--
    Gestion de la une de l'accueil : 4 places (1 principal + 3 secondaires).
    $une = articles mis à la une (le plus récent en premier), $complement = derniers articles qui comblent les places libres.
--}}
@php
    $slots = $une->map(fn($p) => ['post' => $p, 'auto' => false])
        ->concat($complement->map(fn($p) => ['post' => $p, 'auto' => true]))
        ->take(\App\Models\Post::UNE_PLACES)
        ->values();
    $toggle = fn($p, $on) => url('/admin/post/actualite') . '?actualite_une=' . ($on ? 1 : 0) . '&actualite=' . $p->id;
    $thumb = function ($p) {
        $m = $p->getFirstMedia('image');
        if (!$m) return asset('assets_site/img/medc.jpg');
        return $m->hasGeneratedConversion('thumb') ? $m->getUrl('thumb') : $m->getUrl();
    };
@endphp

<section class="une-board" aria-labelledby="une-board-title">
    <div class="une-board__head">
        <div>
            <h2 id="une-board-title" class="une-board__title"><i class="bi bi-star-fill"></i>À la une sur l'accueil</h2>
            <p class="une-board__hint">
                Le dernier article mis à la une devient l'<strong>article principal</strong>. Au-delà de {{ \App\Models\Post::UNE_PLACES }}, le plus ancien quitte la une automatiquement.
                Les places libres affichent les derniers articles publiés.
            </p>
        </div>
        <a href="{{ route('accueil') }}#une-title" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary"><i class="bi bi-box-arrow-up-right me-1"></i>Voir sur le site</a>
    </div>

    <ol class="une-board__slots">
        @for($i = 0; $i < \App\Models\Post::UNE_PLACES; $i++)
            @php $slot = $slots[$i] ?? null; @endphp
            <li class="une-slot {{ $i === 0 ? 'une-slot--lead' : '' }} {{ $slot && $slot['auto'] ? 'is-auto' : '' }} {{ !$slot ? 'is-empty' : '' }}">
                <span class="une-slot__rank">{{ $i === 0 ? '1 · Principal' : ($i + 1) }}</span>

                @if($slot)
                    @php $p = $slot['post']; $t = $p->title ?: Str::limit(strip_tags($p->description), 80); @endphp
                    <img src="{{ $thumb($p) }}" alt="" class="une-slot__img" loading="lazy">
                    <div class="une-slot__body">
                        <a href="{{ route('post.show', $p->id) }}" class="une-slot__title">{{ Str::limit($t, 70) }}</a>
                        <span class="une-slot__meta">
                            @if($slot['auto'])
                                <i class="bi bi-magic"></i> Automatique · dernier publié
                            @else
                                <i class="bi bi-pin-angle-fill"></i> Mis à la une {{ optional($p->une_at)->diffForHumans() }}
                            @endif
                        </span>
                        <div class="une-slot__actions">
                            @if($slot['auto'])
                                <a href="{{ $toggle($p, true) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pin-angle me-1"></i>Épingler</a>
                            @else
                                @if($i > 0)
                                    <a href="{{ $toggle($p, true) }}" class="btn btn-sm btn-outline-primary" title="Devient l'article principal"><i class="bi bi-arrow-up-circle me-1"></i>Principal</a>
                                @endif
                                <a href="{{ $toggle($p, false) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x-lg me-1"></i>Retirer</a>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="une-slot__body une-slot__body--empty">
                        <i class="bi bi-plus-circle"></i>
                        <span>Place libre</span>
                    </div>
                @endif
            </li>
        @endfor
    </ol>
</section>
