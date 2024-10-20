<?php

namespace App\Providers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $this->loadCategories();
        $this->loadRecentPosts();
        $this->loadSurveys();
        $this->loadExternalNews();

        $this->shareDataWithAllViews();
        $this->convertirImage();

        // $this->nettoyerDescriptionsDesPosts();
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
// listes des categories
    private function loadCategories()
    {
        $request = request('type');
        $this->category = Category::with('posts')
            ->when($request == 'sondage', fn($q) => $q->whereTitle('sondage'))
            ->get();
    }

    /**
     * Load recent posts from categories that are not surveys or external news.
     *
     * @return void
     */
    private function loadRecentPosts()
    {
        $excludedCategories = Category::whereIn('title', ['sondage', 'actualites'])->pluck('id');

        $this->post_last = Post::with(['category', 'commentaires', 'media', 'user'])
            ->whereNotIn('category_id', $excludedCategories)
            ->where('published', 'public')
            ->latest()
            ->take(4)
            ->get();
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



    private function loadSurveys() //recuperer les sondages
    {
        $surveyCategory = Category::whereTitle('sondage')->first();

        $this->sondage = Post::with(['category', 'commentaires', 'media', 'user'])
            ->where('category_id', $surveyCategory->id)
            ->where('published', 'public')
            ->latest()
            ->take(4)
            ->get();
    }

    private function loadExternalNews() // recuperer les actualités ---les post en actualité a la une
    {
        $newsCategory = Category::whereSlug('actualites')->first();

        $this->actualite_externe = Post::with(['category', 'commentaires', 'media', 'user'])
            ->where('category_id', $newsCategory->id)
            ->where('published', 'public')
            ->where('actualite_une', 1)
            ->latest()
            ->paginate(10);
    }

    private function shareDataWithAllViews()
    {
        View::composer('*', function ($view) {
            $view->with([
                'category' => $this->category,
                'post_last' => $this->post_last,
                'sondage_front' => $this->sondage,
                'actualite_externe' => $this->actualite_externe,
            ]);
        });
    }
}
