<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Models\CarouselSlide;
use App\Models\Soumission;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Stevebauman\Location\Facades\Location;

class SiteController extends Controller
{
    // Les sondages n'ont pas de title — description est obligatoire pour les listings
    private const LIST_SELECT = ['id', 'title', 'slug', 'description', 'category_id', 'user_id', 'published', 'created_at', 'lien'];

    // Eager loads optimisés pour les listings (syntaxe explicite)
    private function listWith(): array
    {
        return [
            'category' => fn($q) => $q->select('id', 'title', 'slug'),
            'media'    => fn($q) => $q->where('collection_name', 'image'),
        ];
    }

    public function index()
    {
        try {
            $category_actualite = Category::whereSlug('actualites')->first();
            $excludedIds = $category_actualite ? [$category_actualite->id] : [];

            $post = Post::with($this->listWith())
                ->select(self::LIST_SELECT)
                ->withCount('commentaires')
                ->withViewsCount()
                ->where('published', 'public')
                ->whereNotIn('category_id', $excludedIds)
                ->orderBy('created_at', 'desc')
                ->take(12)
                ->get();

            // « À la une » : les articles mis à la une (le plus récent = principal),
            // places libres complétées par les derniers articles, puis le reste pour « Articles récents »
            $une = Post::with($this->listWith())
                ->select(self::LIST_SELECT)
                ->withCount('commentaires')
                ->withViewsCount()
                ->where('published', 'public')
                ->aLaUne()
                ->take(Post::UNE_PLACES)
                ->get();

            $featured = $une->concat($post->whereNotIn('id', $une->pluck('id')))->take(Post::UNE_PLACES);
            $post = $featured->concat($post->whereNotIn('id', $featured->pluck('id')))->values();

            // Carousel depuis la table dédiée ; fallback hero statique si vide
            $slide = CarouselSlide::with(['media' => fn($q) => $q->where('collection_name', 'image')])
                ->where('actif', true)
                ->orderBy('ordre')
                ->orderBy('created_at', 'desc')
                ->get();

            return view('site.pages.accueil', compact('post', 'slide'));
        } catch (\Throwable) {
            return view('site.pages.accueil', ['post' => collect(), 'slide' => collect()]);
        }
    }

    public function post(Request $request)
    {
        try {
            $slug_req     = $request->route('category');
            $category_req = $slug_req ? Category::whereSlug($slug_req)->first() : null;
            if ($slug_req && !$category_req) abort(404);

            $post = Post::with($this->listWith())
                ->select(self::LIST_SELECT)
                ->withCount('commentaires')
                ->withViewsCount()
                ->where('published', 'public')
                ->when($category_req, fn($q) => $q->where('category_id', $category_req->id))
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return view('site.pages.post', compact('post', 'category_req'));
        } catch (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $e) {
            throw $e;
        } catch (\Throwable) {
            return redirect()->route('accueil');
        }
    }

    public function detail(Request $request)
    {
        try {
            $slug_req = $request->route('slug');
            if (!$slug_req) return redirect()->route('accueil');

            // Cache 5 min — invalidé par PostObserver à chaque modification
            $post = Cache::remember("post_detail_{$slug_req}", 300, fn() =>
                Post::with([
                    'category:id,title,slug',
                    'commentaires',
                    'media'         => fn($q) => $q->where('collection_name', 'image'),
                    'user:id,name',
                    'optionSondages',
                ])
                ->withCount('commentaires')
                ->withViewsCount()
                ->whereSlug($slug_req)
                ->where('published', 'public')
                ->first()
            );

            if (!$post) abort(404);

            $statistic_sondage = Cache::remember("post_stats_{$post->id}", 300, fn() =>
                Soumission::with(['optionSondage:id,title'])
                    ->where('post_id', $post->id)
                    ->selectRaw('post_id, option_sondage_id, count(*) as choice')
                    ->groupBy(['post_id', 'option_sondage_id'])
                    ->get()
            );

            $sondage_total = Cache::remember("post_votes_{$post->id}", 300, fn() =>
                Soumission::where('post_id', $post->id)->count()
            );

            // Enregistrement de la vue APRÈS envoi de la réponse (non bloquant)
            $this->scheduleRecordView($post->id, $request->getClientIp());

            // Réponse déjà donnée par ce visiteur (sondages) : on affiche alors les résultats
            $my_vote = null;
            if (strtolower($post->category->title ?? '') === 'sondage') {
                $my_vote = session("poll_votes.{$post->id}")
                    ?? Soumission::where('post_id', $post->id)->where('user_session', $request->session()->getId())->value('option_sondage_id');
            }

            return view('site.pages.detail', compact('post', 'statistic_sondage', 'sondage_total', 'my_vote'));
        } catch (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $e) {
            throw $e; // vraie 404 pour les moteurs de recherche
        } catch (\Throwable) {
            return redirect()->route('accueil');
        }
    }

    /** Ancienne URL /post?category=x → /articles/x (301) */
    public function legacyList(Request $request)
    {
        $params = array_filter(['category' => $request->query('category')]);
        $query  = $request->query('page') ? ['page' => $request->query('page')] : [];

        return redirect()->to(route('post.list', $params) . ($query ? '?' . http_build_query($query) : ''), 301);
    }

    /** Ancienne URL /post/detail?slug=x → /article/x (301) */
    public function legacyDetail(Request $request)
    {
        $slug = $request->query('slug');
        abort_unless($slug && preg_match('/^[A-Za-z0-9\-_]+$/', $slug), 404);

        return redirect()->route('post.detail', ['slug' => $slug], 301);
    }

    /** Manifeste PWA (nom, couleurs, icônes) — reprend Admin > Configuration */
    public function manifest()
    {
        $site = \App\Models\Setting::forViews();
        $nom  = $site['nom'];

        $manifest = [
            'id'               => '/',
            'name'             => $nom . ($site['slogan'] ? ' — ' . $site['slogan'] : ''),
            'short_name'       => Str::limit($nom, 12, ''),
            'description'      => $site['description'] ?: 'Articles médicaux, actualités santé et conseils bien-être.',
            'lang'             => 'fr',
            'dir'              => 'ltr',
            'start_url'        => '/?source=pwa',
            'scope'            => '/',
            'display'          => 'standalone',
            'orientation'      => 'portrait-primary',
            'background_color' => '#ffffff',
            'theme_color'      => '#004A99',
            'categories'       => ['health', 'medical', 'news', 'education'],
            'icons'            => [
                ['src' => asset('assets_site/img/android-chrome-192x192.png'), 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'any'],
                ['src' => asset('assets_site/img/android-chrome-512x512.png'), 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any'],
                ['src' => asset('assets_site/img/android-chrome-512x512.png'), 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'maskable'],
            ],
            'shortcuts'        => [
                ['name' => 'Tous les articles', 'url' => '/articles?source=pwa', 'icons' => [['src' => asset('assets_site/img/android-chrome-192x192.png'), 'sizes' => '192x192']]],
                ['name' => 'Rechercher', 'url' => '/recherche?source=pwa', 'icons' => [['src' => asset('assets_site/img/android-chrome-192x192.png'), 'sizes' => '192x192']]],
                ['name' => 'Contact', 'url' => '/contact?source=pwa', 'icons' => [['src' => asset('assets_site/img/android-chrome-192x192.png'), 'sizes' => '192x192']]],
            ],
        ];

        return response()->json($manifest, 200, ['Content-Type' => 'application/manifest+json; charset=UTF-8'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /** Page affichée par l'application quand il n'y a pas de connexion */
    public function offline()
    {
        return view('site.pages.offline', ['site' => \App\Models\Setting::forViews()]);
    }

    /** Plan du site XML pour Google / Bing (cache 1 h) */
    public function sitemap()
    {
        $xml = Cache::remember('sitemap_xml', 3600, function () {
            $posts = Post::select('slug', 'updated_at', 'created_at')
                ->with(['media' => fn($q) => $q->where('collection_name', 'image')])
                ->where('published', 'public')
                ->whereNotNull('slug')
                ->latest()
                ->get();

            $categories = Category::select('id', 'slug', 'updated_at')->get();

            return view('site.sitemap', compact('posts', 'categories'))->render();
        });

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    public function search(Request $request)
    {
        try {
            $search = trim($request->input('query', ''));
            if (empty($search)) return redirect()->route('accueil');

            $post = Post::with($this->listWith())
                ->select(self::LIST_SELECT)
                ->withCount('commentaires')
                ->where(fn($q) => $q
                    ->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                )
                ->where('published', 'public')
                ->orderBy('created_at', 'desc')
                ->limit(30)
                ->get();

            return view('site.pages.searchPost', compact('post'));
        } catch (\Throwable) {
            return redirect()->route('accueil');
        }
    }

    public function contact(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'name'    => 'required|string|max:100',
                'email'   => 'required|email|max:150',
                'subject' => 'required|string|max:200',
                'message' => 'required|string|max:2000',
            ]);

            ContactMessage::create($request->only('name', 'email', 'subject', 'message'));

            return back()->with('success_contact', 'Votre message a bien été envoyé.');
        }

        return view('site.pages.contact');
    }

    private function scheduleRecordView(int $postId, string $ip): void
    {
        // Exécuté après que PHP-FPM a envoyé la réponse au client
        register_shutdown_function(static function () use ($postId, $ip) {
            // Libère la connexion FastCGI → le navigateur reçoit la page immédiatement
            if (function_exists('fastcgi_finish_request')) {
                fastcgi_finish_request();
            }
            try {
                $post = Post::find($postId);
                if (!$post) return;

                views($post)->record();

                $testIp   = config('app.env') === 'production' ? $ip : '8.8.1.1';
                $location = Cache::remember("ip_geo_{$testIp}", 86400,
                    fn() => Location::get($testIp)
                );

                if ($location) {
                    DB::table('views')
                        ->where('viewable_id', $postId)
                        ->whereNull('ip')
                        ->update([
                            'ip'      => $ip,
                            'country' => $location->countryName ?? null,
                            'city'    => $location->cityName ?? null,
                        ]);
                }
            } catch (\Throwable) {}
        });
    }
}
