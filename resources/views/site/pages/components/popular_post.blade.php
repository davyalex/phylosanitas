<div class="card card-medical p-3 mb-3">
    <h3 class="aside-title text-medical-blue fw-bold mb-3">
        <i class="bi bi-fire me-2"></i>
        Articles Populaires
    </h3>
    @foreach ($post_popular as $item)
        <div class="post-entry-sidebar border-bottom pb-3 mb-3">
            <div class="d-flex gap-3">
                <div class="flex-shrink-0 position-relative">
                    @if ($item->getFirstMediaUrl('image'))
                        <img src="{{ asset($item->getFirstMediaUrl('image')) }}" 
                             class="rounded shadow-sm" 
                             style="width: 70px; height: 70px; object-fit: cover;" 
                             loading="lazy" 
                             alt="{{ $item['title'] }}">
                    @else
                        <img src="{{ asset('assets_site/img/medc.jpg') }}" 
                             class="rounded shadow-sm" 
                             style="width: 70px; height: 70px; object-fit: cover;" 
                             loading="lazy" 
                             alt="{{ $item['title'] }}">
                    @endif
                    <span class="badge bg-danger position-absolute top-0 start-0 m-1" style="font-size: 10px;">
                        <i class="bi bi-eye-fill"></i> {{ views($item)->count() }}
                    </span>
                </div>
                <div class="flex-grow-1">
                    <span class="badge badge-category mb-2">
                        {{ $item['category']['title'] }}
                    </span>
                    <h6 class="mb-2">
                        <a href="/post/detail?slug={{ $item['slug'] }}" 
                           class="text-decoration-none text-dark hover-link">
                            {{ Str::limit($item['title'], 60, '...') }}
                        </a>
                    </h6>
                    <small class="text-muted d-flex justify-content-between">
                        <span>
                            <i class="bi bi-calendar3 text-medical-blue"></i>
                            {{ \Carbon\Carbon::parse($item['created_at'])->diffForHumans() }}
                        </span>
                        <span class="text-health-green">
                            <i class="bi bi-chat-left-quote-fill"></i>
                            {{ $item->commentaires_count }}
                        </span>
                    </small>
                </div>
            </div>
        </div>
    @endforeach
</div>

<style>
.hover-link {
    transition: var(--transition-fast);
}
.hover-link:hover {
    color: var(--medical-blue) !important;
}
.post-entry-sidebar:last-child {
    border-bottom: none !important;
    padding-bottom: 0 !important;
    margin-bottom: 0 !important;
}
</style>
