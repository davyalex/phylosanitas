@extends('admin.layout')
@section('title', request('type') == 'sondage' ? 'Nouveau sondage' : 'Nouvel article')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('post', request('type') == 'sondage' ? ['type' => 'sondage'] : []) }}">{{ request('type') == 'sondage' ? 'Sondages' : 'Articles' }}</a></li>
@endsection

@section('page_header')
<div class="page-bar">
    <div>
        <h1 class="page-bar__title">{{ request('type') == 'sondage' ? 'Nouveau sondage' : 'Nouvel article' }}</h1>
        <p class="page-bar__sub">
            @if(request('type') == 'sondage')
                Posez une question et proposez les réponses possibles. Le sondage est créé en brouillon.
            @else
                Rédigez l'article, choisissez sa catégorie et son image. Vous pouvez le publier tout de suite ou le garder en brouillon.
            @endif
        </p>
    </div>
</div>
@endsection

@section('content')
<form action="{{ route('post.store') }}" method="POST" enctype="multipart/form-data" data-once>
    @csrf
    @if (request('type') == 'sondage')
        @include('admin.pages.sondage.add')
    @else
        @include('admin.pages.post._form', ['post' => null])
    @endif
</form>
@endsection
