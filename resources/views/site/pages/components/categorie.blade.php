<div class="aside-block bg-white p-2">
    <h3 class="aside-title">Categories</h3>
    <ul class="aside-links list-unstyled">
        @foreach ($category as $item)
            <li><a href="/post/category?category={{ $item['slug'] }}"><i class="bi bi-chevron-right"></i>
                    {{ $item['title'] }} <span
                        class="badge rounded-pill bg-info">{{ $item->posts->count() }}</span></a> </li>
        @endforeach
    </ul>
</div>