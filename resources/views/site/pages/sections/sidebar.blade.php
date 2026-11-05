{{-- Sidebar commune. Paramètre optionnel : $showRecent (bool, défaut true) --}}
@php $showRecent = $showRecent ?? true; @endphp

<div class="sidebar">
    @auth
        <a href="{{ route('dashboard') }}" class="profile-link">
            <span class="profile-link__avatar" aria-hidden="true">{{ strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}</span>
            <span class="profile-link__info">
                <strong>{{ Auth::user()->name }}</strong>
                <span>Tableau de bord</span>
            </span>
            <i class="bi bi-chevron-right" aria-hidden="true"></i>
        </a>
    @endauth

    {{-- Masqués sur l'accueil, qui a déjà ses propres blocs gardes et services --}}
    @if($showGarde ?? true)
        @include('site.pages.components.garde')
    @endif

    @if($showServices ?? true)
        @include('site.pages.components.services')
    @endif

    @if(count($actualite_externe ?? []))
        @include('site.pages.components.actualite')
    @endif

    @if(count($sondage_front ?? []))
        @include('site.pages.components.sondage')
    @endif

    @if(count($post_popular ?? []))
        @include('site.pages.components.popular_post')
    @endif

    @if($showRecent && count($post_last ?? []))
        @include('site.pages.components.recent_post')
    @endif

    @include('site.pages.components.categorie')

    <div class="sidebar__sticky">
        <x-ad-slot format="rectangle" />
    </div>
</div>
