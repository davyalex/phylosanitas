<?php

namespace App\Observers;

use App\Models\Category;
use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\Cache;

class CategoryObserver
{
    public function created(Category $category): void  { $this->clearCache(); }
    public function updated(Category $category): void  { $this->clearCache(); }
    public function deleted(Category $category): void  { $this->clearCache(); }
    public function restored(Category $category): void { $this->clearCache(); }
    public function forceDeleted(Category $category): void { $this->clearCache(); }

    private function clearCache(): void
    {
        Cache::forget(AppServiceProvider::CACHE_CATEGORIES);
        Cache::forget(AppServiceProvider::CACHE_RECENT_POSTS);
        Cache::forget(AppServiceProvider::CACHE_POPULAR_POSTS);
        Cache::forget(AppServiceProvider::CACHE_SURVEYS);
        Cache::forget(AppServiceProvider::CACHE_NEWS);
    }
}
