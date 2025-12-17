<?php

namespace App\Observers;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;

class CategoryObserver
{
    /**
     * Handle the Category "created" event.
     */
    public function created(Category $category)
    {
        $this->clearCache();
    }

    /**
     * Handle the Category "updated" event.
     */
    public function updated(Category $category)
    {
        $this->clearCache();
    }

    /**
     * Handle the Category "deleted" event.
     */
    public function deleted(Category $category)
    {
        $this->clearCache();
    }

    /**
     * Handle the Category "restored" event.
     */
    public function restored(Category $category)
    {
        $this->clearCache();
    }

    /**
     * Nettoyer tous les caches liés aux catégories
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
     * Handle the Category "force deleted" event.
     */
    public function forceDeleted(Category $category)
    {
        $this->clearCache();
    }
}
