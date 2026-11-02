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
use App\Http\Controllers\FlashInfoController;
use App\Http\Controllers\PubliciteController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\SettingController;

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
        Route::post('upload-image', 'uploadTinyMCEImage')->name('post.upload-image');
        Route::get('{id}/detail', 'show')->name('post.show')->whereNumber('id');
        Route::delete('commentaire/{id}', 'destroyComment')->name('post.comment.delete')->whereNumber('id'); // les image de tinyMce 


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

    /** Infos flash (bandeau défilant du site) */
    Route::controller(FlashInfoController::class)->prefix('infos-flash')->name('flash.')->group(function () {
        Route::get('/',                 'index')->name('index');
        Route::post('/',                'store')->name('store');
        Route::put('{flash}',           'update')->name('update');
        Route::patch('{flash}/toggle',  'toggle')->name('toggle');
        Route::delete('{flash}',        'destroy')->name('destroy');
    });

    /** Administrateurs uniquement */
    Route::middleware('role:administrateur')->group(function () {

        /** Publicités */
        Route::controller(PubliciteController::class)->prefix('publicites')->name('publicite.')->group(function () {
            Route::get('/',                      'index')->name('index');
            Route::get('create',                 'create')->name('create');
            Route::post('/',                     'store')->name('store');
            Route::get('{publicite}/edit',       'edit')->name('edit');
            Route::put('{publicite}',            'update')->name('update');
            Route::patch('{publicite}/toggle',   'toggle')->name('toggle');
            Route::patch('{publicite}/reset',    'resetStats')->name('reset');
            Route::delete('{publicite}',         'destroy')->name('destroy');
        });

        /** Configuration du site (coordonnées, logo, slogan, réseaux) */
        Route::get('configuration', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('configuration', [SettingController::class, 'update'])->name('settings.update');

        /** Messages de contact & newsletter */
        Route::controller(MessageController::class)->group(function () {
            Route::get('messages',                     'index')->name('messages.index');
            Route::get('messages/{message}',           'show')->name('messages.show');
            Route::patch('messages/{message}/traite',  'markReplied')->name('messages.replied');
            Route::delete('messages/{message}',        'destroy')->name('messages.destroy');
            Route::get('newsletter',                   'newsletter')->name('newsletter.index');
            Route::get('newsletter/export',            'exportNewsletter')->name('newsletter.export');
        });
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

    // URLs lisibles (SEO) : /articles, /articles/{categorie}, /article/{slug}
    route::get('articles/{category?}', 'post')->name('post.list')->where('category', '[a-z0-9\-]+');
    route::get('article/{slug}', 'detail')->name('post.detail')->where('slug', '[A-Za-z0-9\-_]+');

    // Anciennes URLs → redirection permanente (301) pour conserver le référencement
    route::get('post', 'legacyList');
    route::get('post/detail', 'legacyDetail');

    route::get('contact', 'contact')->name('contact');
    route::post('contact', 'contact')->name('contact.store');
    route::match(['get', 'post'], 'recherche', 'search')->name('search');
    route::post('post/search', 'search');

    route::get('sitemap.xml', 'sitemap')->name('sitemap');

    // Application installable (PWA)
    route::get('manifest.webmanifest', 'manifest')->name('manifest');
    route::get('hors-ligne', 'offline')->name('offline');
});

// robots.txt dynamique (pointe vers le sitemap avec le bon domaine)
route::get('robots.txt', function () {
    $lines = [
        'User-agent: *',
        'Disallow: /admin',
        'Disallow: /login',
        'Disallow: /recherche',
        'Disallow: /post/search',
        'Disallow: /pub/',
        '',
        'Sitemap: ' . route('sitemap'),
    ];
    return response(implode("\n", $lines) . "\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
});

// Clic sur une publicité (compté puis redirigé vers l'annonceur)
route::get('pub/{publicite}', [PubliciteController::class, 'click'])->name('publicite.click');

route::post('newsletter/subscribe', [NewsletterController::class, 'store'])->name('newsletter.store');


route::controller(CommentaireController::class)->group(function () {
    route::post('comment', 'store')->name('post.comment');
});
