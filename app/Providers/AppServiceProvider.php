<?php

namespace App\Providers;

use App\Models\Post;
use App\Models\Category;
use App\Observers\PostObserver;
use App\Observers\CategoryObserver;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AppServiceProvider extends ServiceProvider
{
    protected $category;
    protected $post_last;
    protected $post_popular;
    protected $sondage;
    protected $actualite_externe;

    public function register()
    {
        //
    }

    public function boot()
    {
        // Enregistrer les observers
        Category::observe(CategoryObserver::class);
        Post::observe(PostObserver::class);

        // Vérifier si les tables existent avant d'exécuter les requêtes
        if ($this->tablesExist()) {
            $this->loadCategories();
            $this->loadRecentPosts();
            $this->loadPopularPosts();
            $this->loadSurveys();
            $this->loadExternalNews();

            $this->shareDataWithAllViews();
        }

        // Décommenter si besoin de convertir les images base64
        // $this->convertirImage();
    }

    /**
     * Vérifier si les tables nécessaires existent
     */
    private function tablesExist(): bool
    {
        try {
            return Schema::hasTable('categories') &&
                   Schema::hasTable('posts');
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Convertir les images base64 en liens (à utiliser ponctuellement)
     */
    private function convertirImage()
    {
        $posts = Post::all();

        foreach ($posts as $post) {
            preg_match_all('/data:image\/(.*?);base64,([^"]*)/', $post->description, $matches);

            $updatedDescription = $post->description;

            foreach ($matches[0] as $index => $base64) {
                $imageType = $matches[1][$index];
                $imageData = $matches[2][$index];

                $fileName = 'image_' . uniqid() . '.' . $imageType;

                $image = base64_decode($imageData);

                Storage::disk('public')->put($fileName, $image);

                $size = Storage::disk('public')->size($fileName);

                $mediaItem = Media::create([
                    'model_type'             => Post::class,
                    'model_id'               => $post->id,
                    'name'                   => $fileName,
                    'file_name'              => $fileName,
                    'mime_type'              => 'image/' . $imageType,
                    'disk'                   => 'public',
                    'collection_name'        => 'tinyMceImages',
                    'size'                   => $size,
                    'manipulations'          => json_encode([]),
                    'custom_properties'      => json_encode([]),
                    'responsive_images'      => json_encode([]),
                    'generated_conversions'  => json_encode(['optimized' => true]),
                ]);

                $fileUrl = Storage::url($fileName);
                $updatedDescription = str_replace($base64, $fileUrl, $updatedDescription);
            }

            $post->description = $updatedDescription;
            $post->save();
        }
    }

    /**
     * Listes des catégories (avec cache)
     */
    private function loadCategories()
    {
        try {
            $this->category = Cache::remember('categories_list', 3600, function () {
                return Category::select('id', 'title', 'slug')
                    ->withCount(['posts as posts_count' => fn($q) => $q->where('published', 'public')])
                    ->get();
            });
        } catch (\Exception $e) {
            $this->category = collect();
        }
    }

    /**
     * Charger les posts récents (avec cache)
     */
    private function loadRecentPosts()
    {
        try {
            $this->post_last = Cache::remember('recent_posts', 1800, function () {
                $excludedCategories = Category::whereIn('title', ['sondage', 'actualites'])->pluck('id');

                return Post::with([
                        'category:id,title,slug',
                        'user:id,name',
                        'media'
                    ])
                    ->select('id', 'title', 'slug', 'category_id', 'user_id', 'created_at')
                    ->whereNotIn('category_id', $excludedCategories)
                    ->where('published', 'public')
                    ->latest()
                    ->take(4)
                    ->get();
            });
        } catch (\Exception $e) {
            $this->post_last = collect();
        }
    }

    /**
     * Charger les posts populaires (avec cache)
     */
    private function loadPopularPosts()
    {
        try {
            $this->post_popular = Cache::remember('popular_posts', 3600, function () {
                $excludedCategories = Category::whereIn('title', ['sondage', 'actualites'])->pluck('id');

                return Post::with([
                        'category:id,title,slug',
                        'user:id,name',
                        'media'
                    ])
                    ->withCount('commentaires')
                    ->select('id', 'title', 'slug', 'category_id', 'user_id', 'views', 'created_at')
                    ->whereNotIn('category_id', $excludedCategories)
                    ->where('published', 'public')
                    ->orderByViews('desc')
                    ->take(5)
                    ->get();
            });
        } catch (\Exception $e) {
            $this->post_popular = collect();
        }
    }

    /**
     * Récupérer les sondages (avec cache)
     */
    private function loadSurveys()
    {
        try {
            $this->sondage = Cache::remember('surveys_list', 1800, function () {
                $surveyCategory = Category::whereTitle('sondage')->first();

                if ($surveyCategory) {
                    return Post::with([
                            'category:id,title,slug',
                            'user:id,name',
                            'media'
                        ])
                        ->select('id', 'title', 'slug', 'category_id', 'user_id', 'created_at')
                        ->where('category_id', $surveyCategory->id)
                        ->where('published', 'public')
                        ->latest()
                        ->take(4)
                        ->get();
                }

                return collect();
            });
        } catch (\Exception $e) {
            $this->sondage = collect();
        }
    }

    /**
     * Récupérer les actualités externes (avec cache)
     */
    private function loadExternalNews()
    {
        try {
            $this->actualite_externe = Cache::remember('external_news', 900, function () {
                $newsCategory = Category::whereSlug('actualites')->first();

                if ($newsCategory) {
                    return Post::with([
                            'category:id,title,slug',
                            'user:id,name',
                            'media'
                        ])
                        ->select('id', 'title', 'slug', 'category_id', 'user_id', 'created_at', 'actualite_une')
                        ->where('category_id', $newsCategory->id)
                        ->where('published', 'public')
                        ->where('actualite_une', 1)
                        ->latest()
                        ->take(10)
                        ->get();
                }

                return collect();
            });
        } catch (\Exception $e) {
            $this->actualite_externe = collect();
        }
    }

    /**
     * Partager les données avec toutes les vues
     */
    private function shareDataWithAllViews()
    {
        View::composer('*', function ($view) {
            $view->with([
                'category'          => $this->category ?? collect(),
                'post_last'         => $this->post_last ?? collect(),
                'post_popular'      => $this->post_popular ?? collect(),
                'sondage_front'     => $this->sondage ?? collect(),
                'actualite_externe' => $this->actualite_externe ?? collect(),
            ]);
        });
    }
}