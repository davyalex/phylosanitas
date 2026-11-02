{{-- Icône Bootstrap Icons associée à une catégorie. Usage : <x-cat-icon :cat="$post->category" /> --}}
@props(['cat' => null])

@php
    $slug  = strtolower($cat->slug ?? '');
    $title = strtolower($cat->title ?? '');
    $icon  = match (true) {
        str_contains($slug, 'actualite')                                  => 'newspaper',
        str_contains($slug, 'sondage')                                    => 'bar-chart-line',
        str_contains($title, 'public')                                    => 'shield-plus',
        str_contains($title, 'obés') || str_contains($title, 'obes')      => 'person-arms-up',
        str_contains($title, 'thès') || str_contains($title, 'thes')      => 'journal-medical',
        str_contains($title, 'scolaire') || str_contains($title, 'etude') => 'mortarboard',
        str_contains($title, 'nutri') || str_contains($title, 'aliment')  => 'egg-fried',
        str_contains($title, 'sport') || str_contains($title, 'fitness')  => 'heart-pulse',
        str_contains($title, 'savoir')                                    => 'lightbulb',
        default                                                           => 'file-earmark-medical',
    };
@endphp

<i {{ $attributes->merge(['class' => "bi bi-$icon"]) }} aria-hidden="true"></i>
