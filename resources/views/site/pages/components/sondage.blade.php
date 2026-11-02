<section class="widget widget--poll" aria-labelledby="w-poll">
    <div class="widget__head">
        <h2 id="w-poll" class="widget__title"><i class="bi bi-bar-chart-line" aria-hidden="true"></i>Sondages</h2>
        <a href="{{ route('post.list', ['category' => 'sondage']) }}" class="widget__more">Voir tout</a>
    </div>

    <div class="poll-rotator js-rotator" data-interval="8000">
        @foreach ($sondage_front as $item)
            <div class="poll-rotator__item {{ $loop->first ? 'is-active' : '' }}" @unless($loop->first) aria-hidden="true" @endunless>
                <p class="poll-rotator__q">{{ Str::limit(trim(strip_tags($item->description)), 140) }}</p>
                <div class="poll-rotator__foot">
                    <a href="{{ route('post.detail', ['slug' => $item->slug]) }}" class="btn btn-medical btn-sm" @unless($loop->first) tabindex="-1" @endunless>
                        Participer au sondage
                    </a>
                    <time datetime="{{ \Carbon\Carbon::parse($item->created_at)->toDateString() }}">{{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}</time>
                </div>
            </div>
        @endforeach
    </div>

    @if(count($sondage_front) > 1)
        <div class="dots js-rotator-dots" role="group" aria-label="Choisir un sondage">
            @foreach ($sondage_front as $item)
                <button type="button" class="dots__dot {{ $loop->first ? 'is-active' : '' }}" data-index="{{ $loop->index }}" aria-label="Sondage {{ $loop->iteration }}"></button>
            @endforeach
        </div>
    @endif
</section>
