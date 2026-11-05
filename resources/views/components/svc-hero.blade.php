{{--
    En-tête des pages « Services santé ».
    Usage : <x-svc-hero icon="patch-question" titre="Quiz" :parent="['Quiz' => route('quiz.liste')]">Texte d'introduction</x-svc-hero>
    - parent : niveaux intermédiaires du fil d'Ariane (libellé => lien)
    - slot "extra" : contenu sous l'introduction (recherche, méta…)
--}}
@props(['icon' => 'heart-pulse', 'titre', 'parent' => [], 'courant' => null])

<header class="svc-hero">
    <div class="container-xl">
        <nav aria-label="Fil d'Ariane" class="crumbs">
            <a href="{{ route('accueil') }}">Accueil</a>
            @foreach($parent as $label => $url)
                <i class="bi bi-chevron-right" aria-hidden="true"></i>
                <a href="{{ $url }}">{{ $label }}</a>
            @endforeach
            <i class="bi bi-chevron-right" aria-hidden="true"></i>
            <span aria-current="page">{{ Str::limit($courant ?? $titre, 48) }}</span>
        </nav>

        <div class="svc-hero__inner">
            <span class="svc-hero__icon" aria-hidden="true"><i class="bi bi-{{ $icon }}"></i></span>
            <div class="flex-grow-1" style="min-width:0">
                <h1 class="svc-hero__title">{{ $titre }}</h1>
                @if(trim($slot) !== '')<p class="svc-hero__lead">{{ $slot }}</p>@endif
                {{ $extra ?? '' }}
            </div>
        </div>
    </div>
</header>

@include('site.pages.partials.services-quick')
