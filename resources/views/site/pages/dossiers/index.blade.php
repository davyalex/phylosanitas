@extends('site.layout')
@section('title', 'Dossiers santé')
@section('description', 'Nos dossiers santé : des parcours de lecture pour tout comprendre sur un sujet, étape par étape.')

@section('content')
<x-svc-hero icon="collection-play" titre="Dossiers santé">
    Des parcours de lecture pour tout comprendre sur un sujet, étape par étape.
</x-svc-hero>

<section class="svc-body">
    <div class="container-xl">
        @if($dossiers->isEmpty())
            <div class="empty">
                <i class="bi bi-collection-play" aria-hidden="true"></i>
                <h2>Les premiers dossiers arrivent bientôt</h2>
                <p>En attendant, parcourez tous nos articles.</p>
                <a href="{{ route('post.list') }}" class="btn btn-medical">Voir les articles</a>
            </div>
        @else
            <div class="dgrid dgrid--3">
                @foreach($dossiers as $d)
                    @include('site.pages.dossiers._card', ['d' => $d])
                @endforeach
            </div>
        @endif

        <div class="mini-head"><h2>À découvrir aussi</h2></div>
        @include('site.pages.partials.services-grid', ['except' => 'dossiers.index', 'max' => 3])
    </div>
</section>
@endsection
