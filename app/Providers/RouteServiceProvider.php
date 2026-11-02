<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Formulaires publics (newsletter, commentaires, contact, sondages) : limite par IP
        RateLimiter::for('forms', function (Request $request) {
            $tooMany = fn () => redirect()->back()
                ->withInput($request->except(['_token', '_ft', 'site_web']))
                ->with('form_error', 'Trop d\'envois en peu de temps. Réessayez dans quelques minutes.');

            return [
                Limit::perMinute(5)->by('forms-min|' . $request->ip())->response($tooMany),
                Limit::perHour(30)->by('forms-hour|' . $request->ip())->response($tooMany),
            ];
        });

        // Connexion admin : freine les robots qui testent des mots de passe
        RateLimiter::for('login', function (Request $request) {
            $tooMany = function () use ($request) {
                \RealRashid\SweetAlert\Facades\Alert::error('Trop de tentatives de connexion. Réessayez dans une minute.');
                return redirect()->back()->withInput(['phone' => $request->input('phone')]);
            };

            return [
                Limit::perMinute(5)->by('login|' . strtolower((string) $request->input('phone')) . '|' . $request->ip())->response($tooMany),
                Limit::perMinute(20)->by('login-ip|' . $request->ip())->response($tooMany),
            ];
        });
    }
}
