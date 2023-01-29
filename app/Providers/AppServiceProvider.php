<?php

namespace App\Providers;

use App\Models\Category;
use Spatie\Permission\Models\Role;
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
        //
        // $category = Category::with('posts')->get();
        // $role = Role::get();

        // View::composer('*', function ($view) use ($category, $role ) {
        //     $view->with([
        //         'category'=>$category,
        //         'role'=>$role

        //     ]);
        // });
        
    }
}
