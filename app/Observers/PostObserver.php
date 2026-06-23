<?php

namespace App\Observers;

use App\Models\Post;
use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\Cache;

class PostObserver
{
    public function created(Post $post): void  { $this->clearCache(); }
    public function updated(Post $post): void  { $this->clearCache(); }
    public function deleted(Post $post): void  { $this->clearCache(); }
    public function restored(Post $post): void { $this->clearCache(); }
    public function forceDeleted(Post $post): void { $this->clearCache(); }

    private function clearCache(): void
    {
        Cache::forget(AppServiceProvider::CACHE_RECENT_POSTS);
        Cache::forget(AppServiceProvider::CACHE_POPULAR_POSTS);
        Cache::forget(AppServiceProvider::CACHE_SURVEYS);
        Cache::forget(AppServiceProvider::CACHE_NEWS);
    }
}
