@extends('site.layout')
@section('title', 'Calendrier des journées de santé ' . $annee)
@section('description', 'Les grandes journées mondiales et campagnes de santé ' . $annee . ' : en ce moment, à venir et déjà passées. Paludisme, diabète, Octobre rose, Novembre bleu, sida…')

@section('content')
<x-svc-hero icon="calendar-heart" :titre="'Calendrier des journées de santé ' . $annee">
    Les grandes journées et campagnes de santé de l'année : l'occasion de se faire dépister, de se protéger et de s'informer.
    <x-slot:extra>
        @if($total)
            <nav class="fiche__toc" aria-label="Aller à">
                @if($enCours->count())<a href="#en-cours"><i class="bi bi-broadcast" aria-hidden="true"></i>En ce moment</a>@endif
                @if($aVenirParMois->count())<a href="#a-venir"><i class="bi bi-calendar-event" aria-hidden="true"></i>À venir</a>@endif
                @if($passees->count())<a href="#passees"><i class="bi bi-clock-history" aria-hidden="true"></i>Déjà passées</a>@endif
            </nav>
        @endif
    </x-slot:extra>
</x-svc-hero>

<section class="svc-body">
    <div class="container-xl">
        <div class="layout">
            <div class="layout__main">
                @if(!$total)
                    <div class="empty">
                        <i class="bi bi-calendar-heart" aria-hidden="true"></i>
                        <h2>Calendrier en préparation</h2>
                        <p>Revenez bientôt.</p>
                    </div>
                @else
                    {{-- ① En ce moment --}}
                    @if($enCours->count())
                        <section class="cal-group" id="en-cours" aria-labelledby="cal-now">
                            <h2 class="cal-group__title" id="cal-now"><span class="cal-group__dot cal-group__dot--live" aria-hidden="true"></span>En ce moment</h2>
                            <ol class="cal list-unstyled m-0">
                                @foreach($enCours as $j)
                                    @include('site.pages.partials.journee', ['j' => $j])
                                @endforeach
                            </ol>
                        </section>
                    @endif

                    {{-- ② À venir, par mois --}}
                    @if($aVenirParMois->count())
                        <section class="cal-group" id="a-venir" aria-labelledby="cal-next">
                            <h2 class="cal-group__title" id="cal-next"><i class="bi bi-calendar-event" aria-hidden="true"></i>À venir en {{ $anneeAVenir }}</h2>
                            @foreach($aVenirParMois as $mois => $liste)
                                <h3 class="cal-month">{{ ucfirst(\Carbon\Carbon::createFromFormat('Y-m-d', $mois . '-01')->translatedFormat('F')) }}</h3>
                                <ol class="cal list-unstyled m-0">
                                    @foreach($liste as $j)
                                        @include('site.pages.partials.journee', ['j' => $j])
                                    @endforeach
                                </ol>
                            @endforeach
                        </section>
                    @endif

                    {{-- ③ Déjà passées cette année --}}
                    @if($passees->count())
                        <section class="cal-group cal-group--past" id="passees" aria-labelledby="cal-past">
                            <h2 class="cal-group__title" id="cal-past"><i class="bi bi-clock-history" aria-hidden="true"></i>Déjà passées en {{ $annee }}</h2>
                            <ol class="cal list-unstyled m-0">
                                @foreach($passees as $j)
                                    @include('site.pages.partials.journee', ['j' => $j])
                                @endforeach
                            </ol>
                        </section>
                    @endif
                @endif
            </div>
            <aside class="layout__side" aria-label="Compléments">
                @include('site.pages.sections.sidebar', ['showRecent' => false])
            </aside>
        </div>
    </div>
</section>
@endsection
