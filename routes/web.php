<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SondageController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ActualiteController;
use App\Http\Controllers\CarouselController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CommentaireController;
use App\Http\Controllers\NewsletterController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web Routes for your application. These
| Routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


//LOGIN
Route::post('login', [UserController::class, 'login'])->name('login');
Route::get('login', [UserController::class, 'loginForm'])->name('login-form');


//Route admin
Route::middleware('auth')->prefix('admin')->group(function () {

    /**Post */
    Route::controller(DashboardController::class)->group(function () {
        Route::get('', 'index')->name('dashboard');
    });

    Route::controller(UserController::class)->prefix('user')->group(function () {
        Route::get('', 'index')->name('user');
        Route::get('create', 'create')->name('user.create');
        Route::post('store', 'store')->name('user.store');
        Route::get('edit/{slug}', 'edit')->name('user.edit');
        Route::post('update/{id}', 'update')->name('user.update');
        Route::post('destroy/{id}', 'destroy')->name('user.delete');
        Route::get('lock/{id}', 'lock')->name('user.lock');
        Route::get('unlock/{id}', 'unlock')->name('user.unlock');
        Route::get('profil/{id}', 'profil')->name('user.profil');
        route::post('logout', 'logout')->name('logout');
        route::post('newpassword/{id}', 'newpassword')->name('user.newpassword');
    });

    /**Category */
    Route::controller(CategoryController::class)->prefix('category')->group(function () {
        Route::get('', 'index')->name('category');
        Route::post('store', 'store')->name('category.store');
        Route::get('edit/{slug}', 'edit')->name('category.edit');
        Route::post('update/{slug}', 'update')->name('category.update');
        Route::post('destroy/{id}', 'destroy')->name('category.delete');
    });

    /**Post */
    Route::controller(PostController::class)->prefix('post')->group(function () {
        Route::get('', 'index')->name('post');
        Route::get('create', 'create')->name('post.create');
        Route::post('store', 'store')->name('post.store');
        Route::get('edit/{slug}', 'edit')->name('post.edit');
        Route::get('edit-sondage/{id}', 'editSondage')->name('post.edit-sondage');
        Route::post('update-sondage/{id}', 'updateSondage')->name('post.update-sondage');

        Route::post('update/{id}', 'update')->name('post.update');
        Route::post('destroy/{id}', 'destroy')->name('post.delete');
        Route::get('published/{id}', 'published')->name('post.published');
        Route::get('actualite', 'actualite_une'); //Mettre une actualite à la une
        Route::post('upload-image', 'uploadTinyMCEImage')->name('post.upload-image'); // les image de tinyMce 


    });

    Route::controller(CarouselController::class)->prefix('carousel')->group(function () {
        Route::get('/',              'index')->name('actualite.index');
        Route::post('store',         'store')->name('actualite.store');
        Route::post('update/{id}',   'update')->name('actualite.update');
        Route::post('destroy/{id}',  'destroy')->name('actualite.delete');
        Route::get('toggle/{id}',    'toggleActive')->name('actualite.toggle');
        Route::get('up/{id}',        'moveUp')->name('actualite.up');
        Route::get('down/{id}',      'moveDown')->name('actualite.down');
    });
});


/********************************************************************* */

//sondage

route::controller(SondageController::class)->prefix('sondage')->group(function () {
    route::post('index', 'index')->name('sondage.index');
    route::post('store', 'store')->name('sondage.store');
});


//api pour le site
route::controller(SiteController::class)->group(function () {
    route::get('', 'index')->name('accueil');
    route::get('post', 'post')->name('post.list');
    route::get('post/detail', 'detail')->name('post.detail');
    route::get('contact', 'contact')->name('contact');
    route::post('contact', 'contact')->name('contact.store');
    route::post('post/search', 'search')->name('search');
});

route::post('newsletter/subscribe', [NewsletterController::class, 'store'])->name('newsletter.store');


route::controller(CommentaireController::class)->group(function () {
    route::post('comment', 'store')->name('post.comment');
});
