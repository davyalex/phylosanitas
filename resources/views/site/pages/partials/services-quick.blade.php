{{--
    Liens rapides vers les services santé (barre horizontale avec flèches de défilement).
    Le service de la page en cours est mis en évidence.
--}}
@php
    $quick = config('sante.services', []);
    $quickActif = collect(array_keys($quick))->first(fn($r) => request()->routeIs(Str::before($r, '.') . '*'));
@endphp
<nav class="svc-quick" aria-label="Services santé">
    <div class="container-xl">
        <div class="hscroll js-hscroll">
            <span class="topics__label hscroll__label">Services</span>
            <button type="button" class="hscroll__btn hscroll__btn--prev js-hscroll-prev" aria-label="Services précédents" hidden><i class="bi bi-chevron-left" aria-hidden="true"></i></button>
            <div class="topics__track js-hscroll-track">
                @foreach($quick as $route => $s)
                    <a href="{{ route($route) }}" class="chip {{ $quickActif === $route ? 'is-active' : '' }}" @if($quickActif === $route) aria-current="page" @endif>
                        <i class="bi bi-{{ $s['icon'] }}" aria-hidden="true"></i>{{ $s['label'] }}
                    </a>
                @endforeach
            </div>
            <button type="button" class="hscroll__btn hscroll__btn--next js-hscroll-next" aria-label="Services suivants" hidden><i class="bi bi-chevron-right" aria-hidden="true"></i></button>
        </div>
    </div>
</nav>
