@extends('admin.layout')
@section('title', 'Modifier un article')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('post') }}">Articles</a></li>@endsection

@section('page_header')
<div class="page-bar">
    <div>
        <h1 class="page-bar__title">Modifier l'article</h1>
        <p class="page-bar__sub">
            @if($post->published === 'public')
                <span class="status status--live">En ligne</span>
            @else
                <span class="status status--off">Brouillon</span>
            @endif
            · Créé {{ $post->created_at->diffForHumans() }}@if($post->updated_at && $post->updated_at->ne($post->created_at)), modifié {{ $post->updated_at->diffForHumans() }}@endif
        </p>
    </div>
</div>
@endsection

@section('content')
<form action="{{ route('post.update', $post->id) }}" method="POST" enctype="multipart/form-data" data-post-id="{{ $post->id }}" data-once>
    @csrf
    <input type="hidden" name="post_id" value="{{ $post->id }}">
    {{-- post_id exposé pour l'upload d'images TinyMCE --}}
    <script>window.TINYMCE_POST_ID = {{ $post->id }};</script>
    @include('admin.pages.post._form', ['post' => $post])
</form>
@endsection
