<?php

namespace App\Providers;

use App\Models\Post;
use App\Models\Category;
use Spatie\Permission\Models\Role;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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

        // $category = Category::with('posts')->get();

        $request = request('type');
        $category = Category::with('posts')
            ->when(
                $request == 'sondage',
                fn ($q) => $q->whereTitle('sondage')
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
            'category', 'commentaires', 'media', 'user'
        ])->orderBy('created_at', 'desc')
            ->where('category_id', $category_sondage)
            ->where('published', 'public')
            ->get()->take(4);

        //Liste des actualites externes
        $category_actualite = Category::whereSlug('actualites')->first();

        $actualite_externe = Post::with(['category', 'commentaires', 'media', 'user'])->orderBy('created_at', 'desc')
            ->where('category_id', $category_actualite['id'])
            ->where('published', 'public')
            ->get()->take(10);
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
