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

            // Nom du site modifiable depuis Admin > Configuration
            if ($nom = \App\Models\Setting::get('site_nom')) {
                config(['app.name' => $nom]);
            }
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
        View::composer(['site.*', 'admin.*', 'welcome', 'maintenance'], function ($view) {
            $view->with([
                'category'          => $this->getCategories(),
                'post_last'         => $this->getRecentPosts(),
                'post_popular'      => $this->getPopularPosts(),
                'sondage_front'     => $this->getSurveys(),
                'actualite_externe' => $this->getExternalNews(),
                'site'              => \App\Models\Setting::forViews(),
            ]);
        });

        // Compteurs du menu d'administration
        View::composer('admin.layout', function ($view) {
            $view->with('admin_badges', Cache::remember('admin_badges', 60, function () {
                try {
                    return [
                        'messages' => \App\Models\ContactMessage::where('status', 'new')->count(),
                        'flash'    => \App\Models\FlashInfo::visible()->count(),
                        'pubs'     => \App\Models\Publicite::diffusable()->count(),
                        'popups'   => \App\Models\Popup::diffusable()->count(),
                        'questions'=> config('sante.modules.questions') ? \App\Models\QuestionLecteur::where('statut', 'nouvelle')->count() : 0,
                        'gardes'   => \App\Models\Etablissement::actifs()->deGarde()->count(),
                    ];
                } catch (\Throwable) {
                    return ['messages' => 0, 'flash' => 0, 'pubs' => 0, 'popups' => 0, 'questions' => 0, 'gardes' => 0];
                }
            }));
        });

        // Données propres au gabarit public (bandeau infos flash + compteur de visites)
        View::composer('site.layout', function ($view) {
            $view->with([
                'flash_infos' => \App\Models\FlashInfo::forSite(),
                'site_visits' => \App\Http\Middleware\CountSiteVisit::totals(),
                'site_popup'  => \App\Models\Popup::forSite(),
                'journee_sante' => \App\Models\JourneeSante::pourBandeau(),
                'gardes_jour'   => (function () {
                    // Import automatique depuis la feuille Google Sheets (au plus toutes les 6 h, après l'envoi de la page)
                    \App\Services\GardesImport::verifierEnArrierePlan();
                    return \App\Models\Etablissement::gardesDuJour();
                })(),
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
