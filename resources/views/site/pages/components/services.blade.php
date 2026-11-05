{{-- Widget de la barre latérale : liens rapides vers les services santé --}}
@php
    $quick = config('sante.services', []);
    $quickActif = collect(array_keys($quick))->first(fn($r) => request()->routeIs(Str::before($r, '.') . '*'));
@endphp
<div class="widget">
    <div class="widget__head">
        <h2 class="widget__title"><i class="bi bi-grid" aria-hidden="true"></i>Services santé</h2>
    </div>
    <ul class="quick-list">
        @foreach($quick as $route => $s)
            @continue($quickActif === $route)
            <li>
                <a href="{{ route($route) }}">
                    <span class="quick-list__icon" aria-hidden="true"><i class="bi bi-{{ $s['icon'] }}"></i></span>
                    <span>{{ $s['label'] }}</span>
                    <i class="bi bi-chevron-right quick-list__arrow" aria-hidden="true"></i>
                </a>
            </li>
        @endforeach
    </ul>
</div>
