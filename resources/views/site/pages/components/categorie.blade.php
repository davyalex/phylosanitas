<section class="widget" aria-labelledby="w-cat">
    <div class="widget__head">
        <h2 id="w-cat" class="widget__title"><i class="bi bi-folder2-open" aria-hidden="true"></i>Catégories</h2>
    </div>
    <ul class="cat-list">
        @foreach ($category as $item)
            <li>
                <a href="{{ route('post.list', ['category' => $item->slug]) }}" class="{{ request('category') === $item->slug ? 'is-active' : '' }}">
                    <x-cat-icon :cat="$item" />
                    <span>{{ $item->title }}</span>
                    <span class="cat-list__count">{{ $item->posts_count ?? 0 }}</span>
                </a>
            </li>
        @endforeach
    </ul>
</section>
