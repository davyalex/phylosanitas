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
    /**
     * Variables pour partager les données avec les vues
     */
    protected $category;
    protected $post_last;
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
            $this->loadSurveys();
            $this->loadExternalNews();

            $this->shareDataWithAllViews();
        }

        // $this->convertirImage();
        // $this->nettoyerDescriptionsDesPosts();
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
//convertir les image base 64 en lien 
    private function convertirImage(){
        // Récupérer les posts qui contiennent des images en base64
        $posts = Post::all(); // ou utilisez DB::table('posts')->get() selon votre besoin

        foreach ($posts as $post) {
            // Supposons que votre champ de description contient les images en base64
            preg_match_all('/data:image\/(.*?);base64,([^"]*)/', $post->description, $matches);

            // Remplacer les images en base64 par les liens vers les fichiers
            $updatedDescription = $post->description;

            foreach ($matches[0] as $index => $base64) {
                // Extraire le type d'image
                $imageType = $matches[1][$index];
                $imageData = $matches[2][$index];

                // Créer un nom unique pour le fichier
                $fileName = 'image_' . uniqid() . '.' . $imageType;

                // Décoder l'image
                $image = base64_decode($imageData);

                // Enregistrer le fichier sur le disque
                Storage::disk('public')->put($fileName, $image);

                // Obtenir la taille du fichier après l'avoir enregistré
                $size = Storage::disk('public')->size($fileName); // Récupérer la taille du fichier

                // Enregistrer l'image avec Spatie
                $mediaItem = Media::create([
                    'model_type' => Post::class,
                    'model_id' => $post->id,
                    'name' => $fileName,
                    'file_name' => $fileName,
                    'mime_type' => 'image/' . $imageType,
                    'disk' => 'public', // ou le disque que vous utilisez
                    'collection_name' => 'tinyMceImages', // Ajoutez ici la collection_name
                    'size' => $size, // Ajoutez ici la taille du fichier
                    'manipulations' => json_encode([]),
                    'custom_properties' => json_encode([]),
                    'responsive_images' => json_encode([]),
                    'generated_conversions' => json_encode(['optimized' => true]),


                ]);

                // Remplacer le base64 dans la description par l'URL du fichier
                $fileUrl = Storage::url($fileName); // Récupérer l'URL du fichier
                $updatedDescription = str_replace($base64, $fileUrl, $updatedDescription);
            }

            // Mettre à jour la description dans la base de données
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
            $request = request('type');
            
            // Cache les catégories pour 60 minutes
            $cacheKey = 'categories_list_' . ($request ?? 'all');
            
            $this->category = Cache::remember($cacheKey, 3600, function () use ($request) {
                return Category::with('posts')
                    ->when($request == 'sondage', fn($q) => $q->whereTitle('sondage'))
                    ->get();
            });
        } catch (\Exception $e) {
            $this->category = collect();
        }
    }

    /**
     * Load recent posts from categories that are not surveys or external news (avec cache)
     */
    private function loadRecentPosts()
    {
        try {
            // Cache les posts récents pour 30 minutes
            $this->post_last = Cache::remember('recent_posts', 1800, function () {
                $excludedCategories = Category::whereIn('title', ['sondage', 'actualites'])->pluck('id');

                return Post::with(['category', 'commentaires', 'media', 'user'])
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
     * Nettoie la description des posts en supprimant les attributs src et leur contenu.
     *
     * @return void
     */
    // private function nettoyerDescriptionsDesPosts()
    // {
    //     Post::chunk(100, function ($posts) {
    //         foreach ($posts as $post) {
    //             $descriptionNettoyee = preg_replace('/src\s*=\s*"[^"]*"/', '', $post->description);
    //             $post->update(['description' => $descriptionNettoyee]);
    //         }
    //     });
    // }



    /**
     * Récupérer les sondages (avec cache)
     */
    private function loadSurveys()
    {
        try {
            // Cache les sondages pour 30 minutes
            $this->sondage = Cache::remember('surveys_list', 1800, function () {
                $surveyCategory = Category::whereTitle('sondage')->first();

                if ($surveyCategory) {
                    return Post::with(['category', 'commentaires', 'media', 'user'])
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
     * Récupérer les actualités externes (actualités à la une) (avec cache)
     */
    private function loadExternalNews()
    {
        try {
            // Cache les actualités pour 15 minutes (car elles changent plus souvent)
            $this->actualite_externe = Cache::remember('external_news', 900, function () {
                $newsCategory = Category::whereSlug('actualites')->first();

                if ($newsCategory) {
                    return Post::with(['category', 'commentaires', 'media', 'user'])
                        ->where('category_id', $newsCategory->id)
                        ->where('published', 'public')
                        ->where('actualite_une', 1)
                        ->latest()
                        ->paginate(10);
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
                'category' => $this->category ?? collect(),
                'post_last' => $this->post_last ?? collect(),
                'sondage_front' => $this->sondage ?? collect(),
                'actualite_externe' => $this->actualite_externe ?? collect(),
            ]);
        });
    }
}
