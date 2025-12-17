<div class="card card-medical p-3">
    <h3 class="aside-title text-medical-blue fw-bold mb-3">
        <i class="bi bi-folder-fill me-2"></i>
        Catégories
    </h3>
    <ul class="aside-links list-unstyled">
        @foreach ($category as $item)
            <li class="mb-2">
                <a href="/post?category={{ $item['slug'] }}" 
                   class="d-flex align-items-center justify-content-between text-decoration-none p-2 rounded transition-fast">
                    <span class="text-dark">
                        <i class="bi bi-chevron-right text-health-green me-2"></i>
                        {{ $item['title'] }}
                    </span>
                    <span class="badge badge-medical">{{ $item->posts->count() }}</span>
                </a>
            </li>
        @endforeach
    </ul>
</div>

<style>
.aside-links a:hover {
    background: var(--gray-light);
    transform: translateX(5px);
}
</style>