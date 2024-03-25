@extends('site.layout')
@section('title','Accueil')

@section('content')
@include('site.pages.sections.slider')

<div class="d-md-none d-lg-none">
    @include('site.pages.components.actualite')
</div>

@include('site.pages.sections.post')

@endsection