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
                    {{-- view count omis pour éviter N+1 en listing --}}
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
                    <small class="text-muted">
                        <i class="bi bi-calendar3 text-medical-blue"></i>
                        {{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}
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
