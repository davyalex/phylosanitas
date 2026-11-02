<section class="widget" aria-labelledby="w-popular">
    <div class="widget__head">
        <h2 id="w-popular" class="widget__title"><i class="bi bi-graph-up-arrow" aria-hidden="true"></i>Les plus lus</h2>
    </div>
    <div class="widget__list">
        @foreach ($post_popular as $item)
            <x-post-row :post="$item" :rank="$loop->iteration" heading="h3" />
        @endforeach
    </div>
</section>
