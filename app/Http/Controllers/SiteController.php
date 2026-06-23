<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Models\CarouselSlide;
use App\Models\Soumission;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stevebauman\Location\Facades\Location;

class SiteController extends Controller
{
    // Colonnes de base pour les listings (jamais charger description)
    private const LIST_SELECT = ['id', 'title', 'slug', 'category_id', 'user_id', 'published', 'created_at', 'lien'];

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
                ->withCount('commentaires')
                ->select(self::LIST_SELECT)
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
                ->withCount('commentaires')
                ->select(self::LIST_SELECT)
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

            // Page détail : on charge tout (description, commentaires, toutes les medias)
            $post = Post::with([
                    'category:id,title,slug',
                    'commentaires',
                    'media',
                    'user:id,name',
                    'optionSondages',
                ])
                ->whereSlug($slug_req)
                ->where('published', 'public')
                ->first();

            if (!$post) abort(404);

            $statistic_sondage = Soumission::with(['post:id', 'optionSondage:id,title'])
                ->where('post_id', $post->id)
                ->selectRaw('post_id, option_sondage_id, count(*) as choice')
                ->groupBy(['post_id', 'option_sondage_id'])
                ->get();

            $sondage_total = Soumission::where('post_id', $post->id)->count();

            $this->recordView($request, $post);

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
                ->withCount('commentaires')
                ->select(self::LIST_SELECT)
                ->where(fn($q) => $q
                    ->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                )
                ->where('published', 'public')
                ->orderBy('created_at', 'desc')
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

    private function recordView(Request $request, Post $post): void
    {
        try {
            $ip      = $request->getClientIp();
            $testIp  = config('app.env') === 'production' ? $ip : '8.8.1.1';
            $location = Location::get($testIp);

            views($post)->record();

            if ($location) {
                DB::table('views')
                    ->where('viewable_id', $post->id)
                    ->update([
                        'ip'      => $ip,
                        'country' => $location->countryName ?? null,
                        'city'    => $location->cityName ?? null,
                    ]);
            }
        } catch (\Throwable) {
            // Ne pas bloquer si tracking échoue
        }
    }
}
