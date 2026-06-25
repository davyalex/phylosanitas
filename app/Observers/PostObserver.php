<?php

namespace App\Observers;

use App\Models\Post;
use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\Cache;

class PostObserver
{
    public function created(Post $post): void       { $this->clearPostCache($post); $this->clearListCache(); }
    public function updated(Post $post): void       { $this->clearPostCache($post); $this->clearListCache(); }
    public function deleted(Post $post): void       { $this->clearPostCache($post); $this->clearListCache(); }
    public function restored(Post $post): void      { $this->clearPostCache($post); $this->clearListCache(); }
    public function forceDeleted(Post $post): void  { $this->clearPostCache($post); $this->clearListCache(); }

    private function clearPostCache(Post $post): void
    {
        Cache::forget("post_detail_{$post->slug}");
        Cache::forget("post_stats_{$post->id}");
        Cache::forget("post_votes_{$post->id}");
    }

    private function clearListCache(): void
    {
        Cache::forget(AppServiceProvider::CACHE_CATEGORIES);
        Cache::forget(AppServiceProvider::CACHE_RECENT_POSTS);
        Cache::forget(AppServiceProvider::CACHE_POPULAR_POSTS);
        Cache::forget(AppServiceProvider::CACHE_SURVEYS);
        Cache::forget(AppServiceProvider::CACHE_NEWS);
    }
}
