@extends('admin.layout')
@section('title', 'Message')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('messages.index') }}">Messages</a></li>@endsection

@section('page_header')
<div class="page-bar">
    <div>
        <h1 class="page-bar__title">{{ $message->subject }}</h1>
        <p class="page-bar__sub">De <strong>{{ $message->name }}</strong> &lt;{{ $message->email }}&gt; · {{ $message->created_at->translatedFormat('d F Y à H:i') }}</p>
    </div>
    <div class="page-bar__actions">
        <a href="mailto:{{ $message->email }}?subject={{ rawurlencode('Re : ' . $message->subject) }}" class="btn btn-primary"><i class="bi bi-reply me-1"></i>Répondre par email</a>
        @if($message->status !== 'replied')
            <form action="{{ route('messages.replied', $message) }}" method="POST">
                @csrf @method('PATCH')
                <button class="btn btn-outline-success"><i class="bi bi-check2-circle me-1"></i>Marquer comme traité</button>
            </form>
        @endif
        <form action="{{ route('messages.destroy', $message) }}" method="POST" data-confirm="Supprimer ce message ?">
            @csrf @method('DELETE')
            <button class="btn btn-outline-danger"><i class="bi bi-trash me-1"></i>Supprimer</button>
        </form>
    </div>
</div>
@endsection

@section('content')
<div class="panel-a">
    <div class="panel-a__body" style="white-space: pre-line; max-width: 75ch; line-height: 1.7;">{{ $message->message }}</div>
</div>
<a href="{{ route('messages.index') }}" class="btn btn-link px-0 mt-3"><i class="bi bi-arrow-left me-1"></i>Retour aux messages</a>
@endsection
