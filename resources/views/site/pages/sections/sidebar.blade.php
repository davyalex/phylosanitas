<!-- ======= Sidebar ======= -->

{{-- Profil (uniquement si connecté) --}}
@auth
<div class="sidebar-profile mb-3">
    <a href="{{ route('dashboard') }}" class="sidebar-profile__link">
        <div class="sidebar-profile__avatar">
            <i class="bi bi-person-fill"></i>
        </div>
        <div class="sidebar-profile__info">
            <span class="sidebar-profile__name">{{ Auth::user()->name }}</span>
            <span class="sidebar-profile__role">Tableau de bord</span>
        </div>
        <i class="bi bi-chevron-right sidebar-profile__arrow"></i>
    </a>
</div>
@endauth

<!--  posts actualite externe -->
<div class="actualite d-none d-lg-block mb-3">
    @include('site.pages.components.actualite')
</div>

<!--  sondage -->
<div class="mb-3">
    @include('site.pages.components.sondage')
</div>

<!--  posts recent -->
<div class="mb-3">
    @include('site.pages.components.recent_post')
</div>

<!--  posts populaires / plus visités -->
<div class="mb-3">
    @include('site.pages.components.popular_post')
</div>

<!--  Categories -->
<div class="mb-3">
    @include('site.pages.components.categorie')
</div>
