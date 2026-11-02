@extends('site.layout')
@section('title', 'Recherche d\'articles')
@section('robots', 'noindex, follow')

@section('content')
@php $q = request('query'); $count = count($post); @endphp
<section class="section section--tight">
    <div class="container-xl">

        <header class="page-head page-head--search">
            <span class="page-head__icon" aria-hidden="true"><i class="bi bi-search"></i></span>
            <div class="page-head__grow">
                <h1 class="page-head__title">Résultats de recherche</h1>
                <p class="page-head__sub">
                    {{ $count }} {{ $count > 1 ? 'articles trouvés' : 'article trouvé' }} pour «&nbsp;<strong>{{ $q }}</strong>&nbsp;»
                </p>
            </div>
            <form action="{{ route('search') }}" method="GET" class="search-field search-field--page" role="search">
                <i class="bi bi-search" aria-hidden="true"></i>
                <label for="page-search" class="visually-hidden">Nouvelle recherche</label>
                <input id="page-search" type="search" name="query" value="{{ $q }}" minlength="2" required>
                <button type="submit" class="btn btn-medical">Rechercher</button>
            </form>
        </header>

        <div class="layout">
            <div class="layout__main">
                @if($count < 1)
                    <div class="empty">
                        <i class="bi bi-search" aria-hidden="true"></i>
                        <h2>Aucun résultat pour «&nbsp;{{ $q }}&nbsp;»</h2>
                        <ul class="empty__tips">
                            <li>Vérifiez l'orthographe des mots-clés</li>
                            <li>Essayez des termes plus généraux</li>
                            <li>Utilisez moins de mots-clés</li>
                            <li>Parcourez nos catégories dans la barre latérale</li>
                        </ul>
                        <a href="{{ route('accueil') }}" class="btn btn-medical">Retour à l'accueil</a>
                    </div>
                @else
                    <div class="card-grid">
                        @foreach ($post as $item)
                            <x-post-card :post="$item" heading="h2" />
                        @endforeach
                    </div>
                @endif
            </div>

            <aside class="layout__side" aria-label="Compléments">
                @include('site.pages.sections.sidebar')
            </aside>
        </div>
    </div>
</section>
@endsection
