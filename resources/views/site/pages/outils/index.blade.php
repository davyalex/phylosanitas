@extends('site.layout')
@section('title', 'Outils santé gratuits')
@section('description', 'Outils santé gratuits : calcul de l\'IMC, date prévue d\'accouchement, besoin en eau et calendrier de vaccination de l\'enfant.')

@section('content')
<x-svc-hero icon="calculator" titre="Outils santé">
    Des calculs simples pour mieux connaître votre santé. Tout se fait sur votre appareil : rien n'est enregistré.
</x-svc-hero>

<section class="svc-body">
    <div class="container-xl">
        <div class="svc-grid svc-grid--4">
            @foreach($outils as $slug => $o)
                <article class="svc">
                    <span class="svc__icon" aria-hidden="true"><i class="bi bi-{{ $o['icon'] }}"></i></span>
                    <div>
                        <h2 class="svc__title"><a href="{{ route('outils.show', $slug) }}" class="svc__link">{{ $o['titre'] }}</a></h2>
                        <p class="svc__text">{{ $o['texte'] }}</p>
                    </div>
                </article>
            @endforeach
        </div>

        <aside class="disclaimer" aria-label="Avertissement">
            <i class="bi bi-info-circle" aria-hidden="true"></i>
            <p>Ces outils donnent des repères généraux. Ils ne remplacent pas une consultation : en cas de doute, parlez-en à un professionnel de santé.</p>
        </aside>

        <div class="mini-head"><h2>À découvrir aussi</h2></div>
        @include('site.pages.partials.services-grid', ['except' => 'outils.index', 'max' => 3])
    </div>
</section>
@endsection
