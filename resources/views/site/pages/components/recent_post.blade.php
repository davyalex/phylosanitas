<div class="aside-block back-to-top mt-4 bg-white p-2">
    <h3 class="aside-title">Les recents posts</h3>
    @foreach ($post_last as $item)
        <div class="post-entry-1 border-bottom">
            <div class="post-meta">
                <span class="date">{{ $item['category']['title'] }}</span> <span class="mx-1">&bullet;</span>
                <span>publié {{ \Carbon\Carbon::parse($item['created_at'])->diffForHumans() }}</span>
            </div>
            <div class="d-flex align-items-center">
                <img src="{{ asset($item->getFirstMediaUrl('image')) }}" height="50px" width="50px" loading="lazy" alt="">
                <h2 class="mb-2 mx-2"><a href="/post/detail?slug={{ $item['slug'] }}">{{ $item['title'] }}</a></h2>
            </div>
            {{-- <span class="author mb-3 d-block">Jenny Wilson</span> --}}
        </div>
    @endforeach

</div>