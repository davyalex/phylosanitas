@php
    $url = route('auteur.show', $auteur->slug);
    $desc = $auteur->bio ? Str::limit($auteur->bio, 155, '…') : $auteur->nom_public . ($auteur->titre_pro ? ', ' . $auteur->titre_pro : '') . ' — articles santé sur ' . config('app.name') . '.';
@endphp

@extends('site.layout')
@section('title', $auteur->nom_public . ($auteur->titre_pro ? ' — ' . $auteur->titre_pro : ''))
@section('description', $desc)
@section('canonical', $url)
@section('og_type', 'profile')
@if($auteur->photoUrl())
    @section('image', $auteur->photoUrl())
@endif

@push('jsonld')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type'    => 'ProfilePage',
    'url'      => $url,
    'mainEntity' => array_filter([
        '@type'       => 'Person',
        'name'        => $auteur->nom_public,
        'jobTitle'    => $auteur->titre_pro,
        'description' => $auteur->bio,
        'image'       => $auteur->photoUrl(),
        'url'         => $url,
        'worksFor'    => ['@id' => url('/') . '#organization'],
    ]),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@section('content')
<header class="svc-hero">
    <div class="container-xl">
        <nav aria-label="Fil d'Ariane" class="crumbs">
            <a href="{{ route('accueil') }}">Accueil</a>
            <i class="bi bi-chevron-right" aria-hidden="true"></i>
            <span aria-current="page">{{ $auteur->nom_public }}</span>
        </nav>
        <div class="svc-hero__inner">
            <span class="avatar avatar--lg">@if($auteur->photoUrl())<img src="{{ $auteur->photoUrl() }}" alt="">@else{{ $auteur->initiales() }}@endif</span>
            <div class="flex-grow-1" style="min-width:0">
                @if($auteur->est_medecin)<span class="eyebrow eyebrow--light"><i class="bi bi-patch-check-fill" aria-hidden="true"></i>Professionnel de santé</span>@endif
                <h1 class="svc-hero__title">{{ $auteur->nom_public }}</h1>
                @if($auteur->titre_pro)<p class="svc-hero__lead">{{ $auteur->titre_pro }}</p>@endif
                <div class="svc-hero__meta">
                    @if($nbEcrits)<span><i class="bi bi-pencil" aria-hidden="true"></i>{{ $nbEcrits }} article{{ $nbEcrits > 1 ? 's' : '' }} écrit{{ $nbEcrits > 1 ? 's' : '' }}</span>@endif
                    @if($nbRelus)<span><i class="bi bi-patch-check" aria-hidden="true"></i>{{ $nbRelus }} article{{ $nbRelus > 1 ? 's' : '' }} relu{{ $nbRelus > 1 ? 's' : '' }}</span>@endif
                </div>
            </div>
        </div>
    </div>
</header>

@include('site.pages.partials.services-quick')

<section class="svc-body">
    <div class="container-xl">
        <div class="layout">
            <div class="layout__main">
                @if($auteur->bio)
                    <section class="panel mt-0" aria-labelledby="bio-title">
                        <h2 class="panel__title" id="bio-title">À propos</h2>
                        <div class="prose">@foreach(preg_split('/\R{2,}/u', trim((string) $auteur->bio)) as $bloc)@if(trim($bloc) !== '')<p>{!! nl2br(e(trim($bloc))) !!}</p>@endif @endforeach</div>
                    </section>
                @endif

                <div class="mini-head"><h2>Articles</h2></div>
                @if($articles->isEmpty())
                    <p class="muted">Aucun article publié pour le moment.</p>
                @else
                    <div class="card-grid">
                        @foreach($articles as $a)
                            <x-post-card :post="$a" />
                        @endforeach
                    </div>
                    <div class="pager-wrap">{!! $articles->links('vendor.pagination.custom') !!}</div>
                @endif
            </div>
            <aside class="layout__side" aria-label="Compléments">
                @include('site.pages.sections.sidebar', ['showRecent' => false])
            </aside>
        </div>
    </div>
</section>
@endsection
