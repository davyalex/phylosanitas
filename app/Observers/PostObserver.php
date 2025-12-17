<?php

namespace App\Observers;

use App\Models\Post;
use Illuminate\Support\Facades\Cache;

class PostObserver
{
    /**
     * Handle the Post "created" event.
     */
    public function created(Post $post)
    {
        $this->clearCache();
    }

    /**
     * Handle the Post "updated" event.
     */
    public function updated(Post $post)
    {
        $this->clearCache();
    }

    /**
     * Handle the Post "deleted" event.
     */
    public function deleted(Post $post)
    {
        $this->clearCache();
    }

    /**
     * Handle the Post "restored" event.
     */
    public function restored(Post $post)
    {
        $this->clearCache();
    }

    /**
     * Nettoyer tous les caches liés aux posts
     */
    private function clearCache()
    {
        Cache::forget('categories_list_all');
        Cache::forget('categories_list_sondage');
        Cache::forget('recent_posts');
        Cache::forget('surveys_list');
        Cache::forget('external_news');
    }

    /**
     * Handle the Post "force deleted" event.
     */
    public function forceDeleted(Post $post)
    {
        $this->clearCache();
    }
}
