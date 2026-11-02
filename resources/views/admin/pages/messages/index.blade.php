@extends('admin.layout')
@section('title', 'Messages')

@section('page_header')
<div class="page-bar">
    <div>
        <h1 class="page-bar__title">Messages</h1>
        <p class="page-bar__sub">Messages reçus via le formulaire de contact du site.</p>
    </div>
</div>
@endsection

@section('content')
@php
    $labels = ['new' => 'Non lu', 'read' => 'Lu', 'replied' => 'Traité'];
    $total  = $counts->sum();
@endphp

<nav class="tabs-pill" aria-label="Filtrer les messages">
    <a href="{{ route('messages.index') }}" class="{{ !$status ? 'is-active' : '' }}">Tous<span>{{ $total }}</span></a>
    @foreach($labels as $key => $label)
        <a href="{{ route('messages.index', ['status' => $key]) }}" class="{{ $status === $key ? 'is-active' : '' }}">{{ $label }}<span>{{ $counts[$key] ?? 0 }}</span></a>
    @endforeach
</nav>

<div class="panel-a">
    @forelse($messages as $m)
        <a href="{{ route('messages.show', $m) }}" class="inbox-row {{ $m->status === 'new' ? 'is-new' : '' }}">
            <span class="inbox-row__avatar" aria-hidden="true">{{ strtoupper(mb_substr($m->name, 0, 1)) }}</span>
            <span class="d-block" style="min-width:0">
                <span class="inbox-row__from"><strong>{{ $m->name }}</strong> · {{ $m->email }}</span>
                <span class="inbox-row__subject d-block">{{ $m->subject }}</span>
                <span class="inbox-row__excerpt d-block">{{ Str::limit($m->message, 140) }}</span>
            </span>
            <span class="inbox-row__date">
                @if($m->status === 'new')<span class="status status--new mb-1">Non lu</span><br>@elseif($m->status === 'replied')<span class="status status--live mb-1">Traité</span><br>@endif
                {{ $m->created_at->diffForHumans() }}
            </span>
        </a>
    @empty
        <div class="empty-admin">
            <i class="bi bi-inbox"></i>
            <h3>Aucun message</h3>
            <p>Les messages envoyés depuis la page Contact apparaîtront ici.</p>
        </div>
    @endforelse
</div>

<div class="mt-3">{{ $messages->links('pagination::bootstrap-5') }}</div>
@endsection
