@extends('site.layout')
@section('title', 'Page introuvable')
@section('robots', 'noindex, follow')

@section('content')
<section class="section">
    <div class="container-xl">
        <div class="empty empty--page">
            <i class="bi bi-signpost-split" aria-hidden="true"></i>
            <h1>Cette page est introuvable</h1>
            <p>L'article a peut-être été déplacé ou supprimé. Essayez une recherche ou parcourez nos thématiques.</p>
            <form action="{{ route('search') }}" method="GET" class="search-field search-field--page mx-auto mb-4" role="search">
                <i class="bi bi-search" aria-hidden="true"></i>
                <label for="nf-search" class="visually-hidden">Rechercher un article</label>
                <input id="nf-search" type="search" name="query" placeholder="Rechercher un article…" minlength="2" required>
                <button type="submit" class="btn btn-medical">Rechercher</button>
            </form>
            <a href="{{ route('accueil') }}" class="btn btn-outline-medical">Retour à l'accueil</a>
        </div>
    </div>
</section>
@endsection
