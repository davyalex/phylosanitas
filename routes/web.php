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
use App\Http\Controllers\PopupController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\OutilController;
use App\Http\Controllers\MaladieController;
use App\Http\Controllers\DossierController;
use App\Http\Controllers\JourneeSanteController;
use App\Http\Controllers\EtablissementController;
use App\Http\Controllers\QuestionLecteurController;
use App\Http\Controllers\EngagementController;
use App\Http\Controllers\AuteurController;

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
Route::post('login', [UserController::class, 'login'])->name('login')->middleware('throttle:login');
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

    /** Popups d'information (affiche de campagne, annonce…) */
    Route::controller(PopupController::class)->prefix('popups')->name('popup.')->group(function () {
        Route::get('/',                  'index')->name('index');
        Route::get('create',             'create')->name('create');
        Route::post('/',                 'store')->name('store');
        Route::get('{popup}/edit',       'edit')->name('edit');
        Route::put('{popup}',            'update')->name('update');
        Route::patch('{popup}/toggle',   'toggle')->name('toggle');
        Route::patch('{popup}/reset',    'resetStats')->name('reset');
        Route::delete('{popup}',         'destroy')->name('destroy');
    });

    /** Profil public d'un auteur / médecin relecteur */
    Route::post('user/{user}/profil-public', [AuteurController::class, 'updateProfil'])->name('user.profil-public');

    /** Services lecteurs */
    Route::controller(QuestionLecteurController::class)->prefix('questions')->name('question.')->group(function () {
        Route::get('/',                      'index')->name('index');
        Route::get('{question}',             'edit')->name('edit');
        Route::put('{question}',             'update')->name('update');
        Route::patch('{question}/rejeter',   'rejeter')->name('rejeter');
        Route::patch('{question}/depublier', 'depublier')->name('depublier');
        Route::delete('{question}',          'destroy')->name('destroy');
    });

    Route::controller(QuizController::class)->prefix('quiz')->name('quiz.')->group(function () {
        Route::get('/',                'index')->name('index');
        Route::get('create',           'create')->name('create');
        Route::post('/',               'store')->name('store');
        Route::get('{quiz}/edit',      'edit')->name('edit');
        Route::put('{quiz}',           'update')->name('update');
        Route::patch('{quiz}/toggle',  'toggle')->name('toggle');
        Route::delete('{quiz}',        'destroy')->name('destroy');
    });

    Route::controller(MaladieController::class)->prefix('maladies')->name('maladie.')->group(function () {
        Route::get('/',                   'index')->name('index');
        Route::get('create',              'create')->name('create');
        Route::post('/',                  'store')->name('store');
        Route::get('{maladie}/edit',      'edit')->name('edit');
        Route::put('{maladie}',           'update')->name('update');
        Route::patch('{maladie}/toggle',  'toggle')->name('toggle');
        Route::delete('{maladie}',        'destroy')->name('destroy');
    });

    Route::controller(DossierController::class)->prefix('dossiers')->name('dossier.')->group(function () {
        Route::get('/',                   'index')->name('index');
        Route::get('create',              'create')->name('create');
        Route::post('/',                  'store')->name('store');
        Route::get('{dossier}/edit',      'edit')->name('edit');
        Route::put('{dossier}',           'update')->name('update');
        Route::patch('{dossier}/toggle',  'toggle')->name('toggle');
        Route::delete('{dossier}',        'destroy')->name('destroy');
    });

    Route::controller(JourneeSanteController::class)->prefix('journees-sante')->name('journee.')->group(function () {
        Route::get('/',                   'index')->name('index');
        Route::post('/',                  'store')->name('store');
        Route::put('{journee}',           'update')->name('update');
        Route::patch('{journee}/toggle',  'toggle')->name('toggle');
        Route::delete('{journee}',        'destroy')->name('destroy');
    });

    Route::controller(EtablissementController::class)->prefix('annuaire')->name('etablissement.')->group(function () {
        Route::get('/',                             'index')->name('index');
        Route::get('create',                        'create')->name('create');
        Route::post('/',                            'store')->name('store');
        Route::post('garde',                        'garde')->name('garde');
        Route::post('import',                       'importer')->name('import');
        Route::put('import',                        'reglagesImport')->name('import.reglages');
        Route::post('import/fichier',               'importerFichier')->name('import.fichier');
        Route::get('drive/connecter',               'driveConnecter')->name('drive.connect');
        Route::get('drive/callback',                'driveCallback')->name('drive.callback');
        Route::delete('drive',                      'driveDeconnecter')->name('drive.disconnect');
        Route::get('{etablissement}/edit',          'edit')->name('edit');
        Route::put('{etablissement}',               'update')->name('update');
        Route::patch('{etablissement}/toggle',      'toggle')->name('toggle');
        Route::patch('{etablissement}/fin-garde',   'finGarde')->name('fin-garde');
        Route::delete('{etablissement}',            'destroy')->name('destroy');
    });

    /** Administrateurs uniquement */
    Route::middleware('role:administrateur')->group(function () {

        /** Notifications envoyées aux lecteurs abonnés */
        Route::controller(EngagementController::class)->prefix('notifications')->name('push.')->group(function () {
            Route::get('/',          'admin')->name('admin');
            Route::post('envoyer',   'envoyer')->name('envoyer');
            Route::put('reglages',   'reglages')->name('reglages');
        });

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
    route::post('store', 'store')->name('sondage.store')->middleware(['throttle:forms', 'antispam']);
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
    route::post('contact', 'contact')->name('contact.store')->middleware(['throttle:forms', 'antispam']);
    route::match(['get', 'post'], 'recherche', 'search')->name('search');
    route::post('post/search', 'search');

    route::get('sitemap.xml', 'sitemap')->name('sitemap');
    route::get('politique-de-confidentialite', 'privacy')->name('privacy');

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
        'Disallow: /popup/',
        '',
        'Sitemap: ' . route('sitemap'),
    ];
    return response(implode("\n", $lines) . "\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
});

/********************************************************************* */
// Services lecteurs (site public)

route::controller(QuizController::class)->group(function () {
    route::get('quiz', 'liste')->name('quiz.liste');
    route::get('quiz/{slug}', 'jouer')->name('quiz.jouer')->where('slug', '[a-z0-9\-]+');
    route::post('quiz/{quiz}/score', 'score')->name('quiz.score')->middleware('throttle:20,1');
});

route::get('outils-sante', [OutilController::class, 'index'])->name('outils.index');
route::get('outils-sante/{outil}', [OutilController::class, 'show'])->name('outils.show')->where('outil', '[a-z\-]+');

route::get('maladies', [MaladieController::class, 'liste'])->name('maladies.index');
route::get('maladies/{slug}', [MaladieController::class, 'fiche'])->name('maladies.show')->where('slug', '[a-z0-9\-]+');

route::get('dossiers', [DossierController::class, 'liste'])->name('dossiers.index');
route::get('dossiers/{slug}', [DossierController::class, 'voir'])->name('dossiers.show')->where('slug', '[a-z0-9\-]+');

route::get('calendrier-sante', [JourneeSanteController::class, 'calendrier'])->name('calendrier');
route::get('annuaire-sante', [EtablissementController::class, 'annuaire'])->name('annuaire');
route::get('pharmacies-de-garde', [EtablissementController::class, 'pharmaciesDeGarde'])->name('garde');
route::get('auteur/{slug}', [AuteurController::class, 'voir'])->name('auteur.show')->where('slug', '[a-z0-9\-]+');

route::controller(QuestionLecteurController::class)->group(function () {
    route::get('le-medecin-repond', 'liste')->name('questions.index');
    route::post('le-medecin-repond', 'envoyer')->name('questions.store')->middleware(['throttle:forms', 'antispam']);
    route::get('le-medecin-repond/{slug}', 'voir')->name('questions.show')->where('slug', '[a-z0-9\-]+');
});

route::controller(EngagementController::class)->group(function () {
    route::post('article/{post}/avis', 'avis')->name('post.avis')->middleware('throttle:20,1')->whereNumber('post');
    route::get('push/cle', 'cle')->name('push.cle');
    route::post('push/abonnement', 'abonner')->name('push.abonner')->middleware('throttle:10,1');
    route::post('push/desabonnement', 'desabonner')->name('push.desabonner')->middleware('throttle:10,1');
    route::get('push/derniere', 'derniere')->name('push.derniere');
});

// Clic sur une publicité (compté puis redirigé vers l'annonceur)
route::get('pub/{publicite}', [PubliciteController::class, 'click'])->name('publicite.click');

// Popup d'information : affichage compté (appel JS) et clic sur le bouton
route::post('popup/{popup}/vue', [PopupController::class, 'vue'])->name('popup.vue')->middleware('throttle:20,1');
route::get('popup/{popup}/clic', [PopupController::class, 'clic'])->name('popup.clic');

// Formulaires publics : limite d'envois + protection anti-robots (<x-honeypot /> dans chaque formulaire)
route::post('newsletter/subscribe', [NewsletterController::class, 'store'])->name('newsletter.store')->middleware(['throttle:forms', 'antispam']);


route::controller(CommentaireController::class)->group(function () {
    route::post('comment', 'store')->name('post.comment')->middleware(['throttle:forms', 'antispam']);
});
