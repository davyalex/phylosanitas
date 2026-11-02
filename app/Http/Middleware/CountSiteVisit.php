<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Compte les visites du site public :
 *  - 1 visite par session (navigateur) et par jour
 *  - 1 page vue par affichage de page
 * Les robots, l'administration et les requêtes AJAX/non-GET sont ignorés.
 * L'écriture se fait après l'envoi de la réponse (terminate).
 */
class CountSiteVisit
{
    public const CACHE_TOTALS = 'site_visit_totals';

    private const BOTS = '/bot|crawl|spider|slurp|facebookexternalhit|whatsapp|preview|lighthouse|headless|curl|wget|python|monitor/i';

    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }

    public function terminate(Request $request, $response): void
    {
        if (!$this->shouldCount($request, $response)) return;

        try {
            $today    = now()->toDateString();
            $newVisit = false;

            if ($request->hasSession() && $request->session()->get('visit_day') !== $today) {
                $request->session()->put('visit_day', $today);
                $request->session()->save();
                $newVisit = true;
            }

            DB::statement(
                'INSERT INTO site_visit_stats (`date`, visites, pages_vues) VALUES (?, ?, 1)
                 ON DUPLICATE KEY UPDATE visites = visites + VALUES(visites), pages_vues = pages_vues + 1',
                [$today, $newVisit ? 1 : 0]
            );
        } catch (\Throwable) {
            // Le comptage ne doit jamais casser le site
        }
    }

    private function shouldCount(Request $request, $response): bool
    {
        if (!$request->isMethod('GET') || $request->ajax() || $request->prefetch()) return false;
        if ($request->is('admin*', 'login', 'sitemap.xml', 'robots.txt', 'manifest.webmanifest', 'hors-ligne', 'pub/*', 'storage/*')) return false;
        if (method_exists($response, 'getStatusCode') && $response->getStatusCode() !== 200) return false;
        if (preg_match(self::BOTS, (string) $request->userAgent())) return false;
        if ($request->user()) return false; // l'équipe ne gonfle pas les chiffres

        return true;
    }

    /** Totaux pour l'affichage public (cache 10 min) */
    public static function totals(): array
    {
        return Cache::remember(self::CACHE_TOTALS, 600, function () {
            try {
                $row = DB::table('site_visit_stats')
                    ->selectRaw('COALESCE(SUM(visites),0) as visites, COALESCE(SUM(pages_vues),0) as pages_vues')
                    ->first();

                // + visites antérieures saisies dans Admin > Configuration (ex. Google Analytics)
                $depart = (int) \App\Models\Setting::get('visites_depart', 0);

                return [
                    'visites'    => (int) $row->visites + $depart,
                    'pages_vues' => (int) $row->pages_vues,
                    'mesurees'   => (int) $row->visites,
                ];
            } catch (\Throwable) {
                return ['visites' => 0, 'pages_vues' => 0];
            }
        });
    }
}
