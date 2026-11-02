<section class="widget" aria-labelledby="w-recent">
    <div class="widget__head">
        <h2 id="w-recent" class="widget__title"><i class="bi bi-clock-history" aria-hidden="true"></i>Articles récents</h2>
        <a href="{{ route('post.list') }}" class="widget__more">Voir tout</a>
    </div>
    <div class="widget__list">
        @foreach ($post_last as $item)
            <x-post-row :post="$item" heading="h3" />
        @endforeach
    </div>
</section>
