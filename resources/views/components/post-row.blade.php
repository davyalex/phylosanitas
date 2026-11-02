{{--
    Ligne article compacte (miniature + titre). Utilisée dans la sidebar et la une.
    Usage : <x-post-row :post="$item" :rank="1" />
--}}
@props(['post', 'rank' => null, 'heading' => 'h4'])

@php
    $title = $post->title ?: Str::limit(strip_tags($post->description ?? ''), 80);
    $views = (int) ($post->views_count ?? 0);
@endphp

<article {{ $attributes->merge(['class' => 'prow']) }}>
    <div class="prow__media">
        <x-media-img :model="$post" :alt="$title" sizes="128px" width="160" height="120" />
        @if($rank)
            <span class="prow__rank" aria-label="Position {{ $rank }}">{{ $rank }}</span>
        @endif
    </div>
    <div class="prow__body">
        @if($post->category)
            <span class="prow__cat">{{ $post->category->title }}</span>
        @endif
        <{{ $heading }} class="prow__title">
            <a href="{{ route('post.detail', ['slug' => $post->slug]) }}" class="prow__link">{{ $title }}</a>
        </{{ $heading }}>
        <div class="meta">
            <time datetime="{{ \Carbon\Carbon::parse($post->created_at)->toDateString() }}">{{ \Carbon\Carbon::parse($post->created_at)->diffForHumans() }}</time>
            <span><i class="bi bi-eye" aria-hidden="true"></i>{{ number_format($views, 0, ',', ' ') }}</span>
        </div>
    </div>
</article>
