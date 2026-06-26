<?php

namespace App\Providers;

use App\Models\Post;
use App\Models\Category;
use App\Observers\PostObserver;
use App\Observers\CategoryObserver;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    // Clés de cache — centralisées ici et dans les observers
    public const CACHE_CATEGORIES    = 'categories_list';
    public const CACHE_RECENT_POSTS  = 'recent_posts';
    public const CACHE_POPULAR_POSTS = 'popular_posts';
    public const CACHE_SURVEYS       = 'surveys_list';
    public const CACHE_NEWS          = 'external_news';

    // Media optimisé : seulement l'image de couverture
    private static function imageOnly(): \Closure
    {
        return fn($q) => $q->where('collection_name', 'image');
    }

    public function register(): void {}

    public function boot(): void
    {
        Category::observe(CategoryObserver::class);
        Post::observe(PostObserver::class);

        if ($this->tablesExist()) {
            $this->shareDataWithAllViews();
        }
    }

    private function tablesExist(): bool
    {
        try {
            return Schema::hasTable('categories') && Schema::hasTable('posts');
        } catch (\Exception) {
            return false;
        }
    }

    private function shareDataWithAllViews(): void
    {
        View::composer('*', function ($view) {
            $view->with([
                'category'          => $this->getCategories(),
                'post_last'         => $this->getRecentPosts(),
                'post_popular'      => $this->getPopularPosts(),
                'sondage_front'     => $this->getSurveys(),
                'actualite_externe' => $this->getExternalNews(),
            ]);
        });
    }

    private function getCategories()
    {
        return Cache::remember(self::CACHE_CATEGORIES, 3600, function () {
            try {
                return Category::select('id', 'title', 'slug')
                    ->withCount('posts')
                    ->get();
            } catch (\Exception) {
                return collect();
            }
        });
    }

    private function getRecentPosts()
    {
        return Cache::remember(self::CACHE_RECENT_POSTS, 1800, function () {
            try {
                $excludedIds = Category::whereIn('title', ['sondage', 'actualites'])->pluck('id');

                return Post::with([
                        'category' => fn($q) => $q->select('id', 'title', 'slug'),
                        'media'    => self::imageOnly(),
                    ])
                    ->select('id', 'title', 'slug', 'category_id', 'user_id', 'created_at')
                    ->withCount('commentaires')
                    ->withViewsCount()
                    ->whereNotIn('category_id', $excludedIds)
                    ->where('published', 'public')
                    ->latest()
                    ->take(4)
                    ->get();
            } catch (\Exception) {
                return collect();
            }
        });
    }

    private function getPopularPosts()
    {
        return Cache::remember(self::CACHE_POPULAR_POSTS, 3600, function () {
            try {
                $excludedIds = Category::whereIn('title', ['sondage', 'actualites'])->pluck('id');

                return Post::with([
                        'category' => fn($q) => $q->select('id', 'title', 'slug'),
                        'media'    => self::imageOnly(),
                    ])
                    ->select('id', 'title', 'slug', 'category_id', 'user_id', 'created_at')
                    ->withCount('commentaires')
                    ->withViewsCount()
                    ->whereNotIn('category_id', $excludedIds)
                    ->where('published', 'public')
                    ->orderByViews('desc')
                    ->take(5)
                    ->get();
            } catch (\Exception) {
                return collect();
            }
        });
    }

    private function getSurveys()
    {
        return Cache::remember(self::CACHE_SURVEYS, 1800, function () {
            try {
                $surveyCategory = Category::whereTitle('sondage')->first();
                if (!$surveyCategory) return collect();

                return Post::with([
                        'category' => fn($q) => $q->select('id', 'title', 'slug'),
                        'media'    => self::imageOnly(),
                    ])
                    ->select('id', 'title', 'slug', 'description', 'category_id', 'user_id', 'created_at')
                    ->where('category_id', $surveyCategory->id)
                    ->where('published', 'public')
                    ->latest()
                    ->take(4)
                    ->get();
            } catch (\Exception) {
                return collect();
            }
        });
    }

    private function getExternalNews()
    {
        return Cache::remember(self::CACHE_NEWS, 900, function () {
            try {
                $newsCategory = Category::whereSlug('actualites')->first();
                if (!$newsCategory) return collect();

                return Post::with([
                        'category' => fn($q) => $q->select('id', 'title', 'slug'),
                        'media'    => self::imageOnly(),
                    ])
                    ->select('id', 'title', 'slug', 'category_id', 'user_id', 'created_at', 'actualite_une')
                    ->where('category_id', $newsCategory->id)
                    ->where('published', 'public')
                    ->where('actualite_une', 1)
                    ->latest()
                    ->take(10)
                    ->get();
            } catch (\Exception) {
                return collect();
            }
        });
    }
}
