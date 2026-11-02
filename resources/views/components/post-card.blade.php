{{--
    Carte article (grille). Toute la carte est cliquable via le lien du titre.
    Usage : <x-post-card :post="$item" />
--}}
@props(['post', 'heading' => 'h3'])

@php
    $title = $post->title ?: Str::limit(strip_tags($post->description ?? ''), 90);
    $url   = route('post.detail', ['slug' => $post->slug]);
    $views = (int) ($post->views_count ?? 0);
    $comms = (int) ($post->commentaires_count ?? 0);
@endphp

<article {{ $attributes->merge(['class' => 'pcard']) }}>
    <div class="pcard__media">
        <x-media-img :model="$post" :alt="$title"
                     sizes="(min-width: 1200px) 280px, (min-width: 576px) 45vw, 112px" />
        @if($post->category)
            <span class="pcard__cat">
                <x-cat-icon :cat="$post->category" />
                {{ $post->category->title }}
            </span>
        @endif
    </div>
    <div class="pcard__body">
        @if($post->category)
            <span class="pcard__cat-inline">{{ $post->category->title }}</span>
        @endif
        <{{ $heading }} class="pcard__title">
            <a href="{{ $url }}" class="pcard__link">{{ $title }}</a>
        </{{ $heading }}>
        <div class="meta">
            <time datetime="{{ \Carbon\Carbon::parse($post->created_at)->toDateString() }}">
                <i class="bi bi-calendar3" aria-hidden="true"></i>{{ \Carbon\Carbon::parse($post->created_at)->diffForHumans() }}
            </time>
            <span title="Vues"><i class="bi bi-eye" aria-hidden="true"></i>{{ number_format($views, 0, ',', ' ') }}<span class="visually-hidden"> vues</span></span>
            @if($comms > 0)
                <span title="Commentaires"><i class="bi bi-chat-left-text" aria-hidden="true"></i>{{ $comms }}<span class="visually-hidden"> commentaires</span></span>
            @endif
        </div>
    </div>
</article>
