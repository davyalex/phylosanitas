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
        
        $category = Category::with('posts')->get();
        $post_last = Post::with(['category', 'commentaires', 'media', 'user'])->orderBy('created_at', 'desc')->get()->take(4);

        View::composer('*', function ($view) use ($category, $post_last) {
            $view->with([
                'category'=>$category,
            ]);

            $view->with([
                'post_last'=>$post_last,
            ]);
        });
        

        // Paginator::defaultView('view-name');
        // Paginator::defaultSimpleView('view-name');
    }
}
