{{--
    Grille des services santé.
    Paramètres : $except (route à exclure), $max (nombre de tuiles), $class (classes de la grille)
--}}
@php
    $svcList = collect(config('sante.services', []))
        ->except($except ?? [])
        ->take($max ?? 99);
@endphp
<div class="svc-grid {{ $class ?? '' }}">
    @foreach($svcList as $route => $s)
        <article class="svc">
            <span class="svc__icon" aria-hidden="true"><i class="bi bi-{{ $s['icon'] }}"></i></span>
            <div>
                <h3 class="svc__title"><a href="{{ route($route) }}" class="svc__link">{{ $s['label'] }}</a></h3>
                <p class="svc__text">{{ $s['texte'] }}</p>
            </div>
        </article>
    @endforeach
</div>
