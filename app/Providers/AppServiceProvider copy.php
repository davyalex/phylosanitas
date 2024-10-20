<?php

namespace App\Providers;

use App\Models\Post;
use App\Models\Category;
use Spatie\Permission\Models\Role;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {



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
                    'generated_conversions' => json_encode(['optimized'=>true]),

                    
                ]);

                // Remplacer le base64 dans la description par l'URL du fichier
                $fileUrl = Storage::url($fileName); // Récupérer l'URL du fichier
                $updatedDescription = str_replace($base64, $fileUrl, $updatedDescription);
            }

            // Mettre à jour la description dans la base de données
            $post->description = $updatedDescription;
            $post->save();
        }

        // $category = Category::with('posts')->get();

        $request = request('type');
        $category = Category::with('posts')
            ->when(
                $request == 'sondage',
                fn($q) => $q->whereTitle('sondage')
            )
            ->get();

        //Liste des articles recents
        $category_sondage = Category::whereTitle('sondage')->first();
        $category_sondage = $category_sondage['id'];

        $category_actualite = Category::whereSlug('actualites')->first();
        $category_actualite = $category_actualite['id'];

        $post_last = Post::with(['category', 'commentaires', 'media', 'user'])->orderBy('created_at', 'desc')
            ->whereNotIn('category_id', [$category_sondage, $category_actualite])
            ->where('published', 'public')
            ->get()->take(4);

        //Liste des sondages pour le front
        $sondage = Post::with([
            'category',
            'commentaires',
            'media',
            'user'
        ])->orderBy('created_at', 'desc')
            ->where('category_id', $category_sondage)
            ->where('published', 'public')
            ->get()->take(4);

        //Liste des actualites externes
        $category_actualite = Category::whereSlug('actualites')->first();

        $actualite_externe = Post::with(['category', 'commentaires', 'media', 'user'])->orderBy('created_at', 'desc')
            ->where('category_id', $category_actualite['id'])
            ->where('published', 'public')
            ->where('actualite_une', 1)
            ->orderBy('created_at', 'desc')->paginate(10);
        // dd($category_actualite->toArray());

        View::composer('*', function ($view) use ($category, $post_last, $sondage, $actualite_externe) {
            $view->with([
                'category' => $category,
                'post_last' => $post_last,
                'sondage_front' => $sondage,
                'actualite_externe' => $actualite_externe,

            ]);
        });


        // Paginator::defaultView('view-name');
        // Paginator::defaultSimpleView('view-name');
    }



    
}
