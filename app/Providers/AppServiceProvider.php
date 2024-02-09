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

        $post_last = Post::with(['category', 'commentaires', 'media', 'user'])->orderBy('created_at', 'desc')
            ->where('category_id', '!=', $category_sondage['id'])
            ->where('published', 'public')
            ->get()->take(4);

        //Liste des sondages pour le front
        $sondage = Post::with([
            'category', 'commentaires', 'media', 'user'
        ])->orderBy('created_at', 'desc')
            ->where('category_id', $category_sondage['id'])
            ->where('published', 'public')
            ->get()->take(4);


        View::composer('*', function ($view) use ($category, $post_last, $sondage) {
            $view->with([
                'category' => $category,
                'post_last' => $post_last,
                'sondage_front' => $sondage,
            ]);
        });


        // Paginator::defaultView('view-name');
        // Paginator::defaultSimpleView('view-name');
    }
}
