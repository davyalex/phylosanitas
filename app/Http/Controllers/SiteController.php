<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Models\CarouselSlide;
use App\Models\Soumission;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
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
                ->where('published', 'public')
                ->whereNotIn('category_id', $excludedIds)
                ->orderBy('created_at', 'desc')
                ->take(12)
                ->get();

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
            $slug_req     = $request->input('category');
            $category_req = $slug_req ? Category::whereSlug($slug_req)->first() : null;

            $post = Post::with($this->listWith())
                ->select(self::LIST_SELECT)
                ->withCount('commentaires')
                ->where('published', 'public')
                ->when($category_req, fn($q) => $q->where('category_id', $category_req->id))
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return view('site.pages.post', compact('post', 'category_req'));
        } catch (\Throwable) {
            return redirect()->route('accueil');
        }
    }

    public function detail(Request $request)
    {
        try {
            $slug_req = $request->input('slug');
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

            return view('site.pages.detail', compact('post', 'statistic_sondage', 'sondage_total'));
        } catch (\Throwable) {
            return redirect()->route('accueil');
        }
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
