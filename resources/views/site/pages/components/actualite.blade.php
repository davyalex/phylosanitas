<section class="widget" aria-labelledby="w-actu">
    <div class="widget__head">
        <h2 id="w-actu" class="widget__title"><i class="bi bi-newspaper" aria-hidden="true"></i>Actualités</h2>
        <a href="{{ route('post.list', ['category' => 'actualites']) }}" class="widget__more">Voir tout</a>
    </div>

    <div class="swiper newsSwiper">
        <div class="swiper-wrapper">
            @foreach ($actualite_externe as $item)
                <div class="swiper-slide">
                    <article class="news-card">
                        <x-media-img :model="$item" :alt="$item->title" sizes="(min-width: 992px) 340px, 100vw" width="800" height="500" />
                        <div class="news-card__body">
                            <h3 class="news-card__title">
                                <a href="{{ route('post.detail', ['slug' => $item->slug]) }}" class="news-card__link">{{ Str::limit($item->title, 80) }}</a>
                            </h3>
                            <time class="news-card__time" datetime="{{ \Carbon\Carbon::parse($item->created_at)->toDateString() }}">{{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}</time>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
        @if(count($actualite_externe) > 1)
            <div class="widget__nav">
                <div class="swiper-pagination news-pagination"></div>
                <div class="widget__arrows">
                    <button type="button" class="mini-arrow news-prev" aria-label="Actualité précédente"><i class="bi bi-chevron-left" aria-hidden="true"></i></button>
                    <button type="button" class="mini-arrow news-next" aria-label="Actualité suivante"><i class="bi bi-chevron-right" aria-hidden="true"></i></button>
                </div>
            </div>
        @endif
    </div>
</section>
