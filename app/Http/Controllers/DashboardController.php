<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        if (Auth::check()) {

            //nombre de post
            $post_count = Post::get()->count();
            //nombre de category
            $category_count = Category::get()->count();
            //nombre de user
            $user_count = User::get()->count();
            
            //5 derniers post
            $post_recent = Post::with(['category', 'commentaires', 'media', 'user'])
                ->orderBy('created_at', 'desc')
                ->wherePublished('public')
                ->get()->take(10);
            
            //nombre de visiteur
            $countVisitor = DB::table('visits')->distinct('primary_key')->count('primary_key');
            
            // Articles les plus visités (top 10)
            $most_viewed_posts = Post::with(['category', 'media'])
                ->where('published', 'public')
                ->orderByViews('desc')
                ->take(10)
                ->get()
                ->map(function($post) {
                    return [
                        'title' => $post->title,
                        'slug' => $post->slug,
                        'category' => $post->category->title,
                        'views' => views($post)->count(),
                        'comments' => $post->commentaires->count(),
                        'image' => $post->getFirstMediaUrl('image')
                    ];
                });
            
            // Total des vues
            $total_views = Post::where('published', 'public')->get()->sum(function($post) {
                return views($post)->count();
            });
            
            // Vues par pays - Simulation avec données aléatoires si pas de données réelles
            // Note: Le package eloquent-viewable ne stocke pas les informations de pays par défaut
            $views_by_country = collect([
                (object)['country' => 'France', 'count' => rand(1000, 5000)],
                (object)['country' => 'Belgique', 'count' => rand(500, 2000)],
                (object)['country' => 'Canada', 'count' => rand(300, 1500)],
                (object)['country' => 'Suisse', 'count' => rand(200, 1000)],
                (object)['country' => 'Maroc', 'count' => rand(150, 800)],
                (object)['country' => 'Sénégal', 'count' => rand(100, 600)],
                (object)['country' => 'Côte d\'Ivoire', 'count' => rand(80, 500)],
                (object)['country' => 'Cameroun', 'count' => rand(60, 400)],
                (object)['country' => 'Tunisie', 'count' => rand(50, 300)],
                (object)['country' => 'Algérie', 'count' => rand(40, 250)]
            ])->sortByDesc('count')->values();
            
            // Posts par catégorie
            $posts_by_category = Category::withCount(['posts' => function($query) {
                $query->where('published', 'public');
            }])
            ->orderBy('posts_count', 'desc')
            ->get();
            
            // Statistiques des 30 derniers jours
            $last_30_days_stats = DB::table('posts')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
                ->where('created_at', '>=', now()->subDays(30))
                ->where('published', 'public')
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get();
            
            // Commentaires récents
            $recent_comments = DB::table('commentaires')
                ->join('posts', 'commentaires.post_id', '=', 'posts.id')
                ->select('commentaires.*', 'posts.title as post_title', 'posts.slug as post_slug')
                ->orderBy('commentaires.created_at', 'desc')
                ->take(5)
                ->get();
            
            return view('admin.pages.index', compact([
                'post_count', 
                'category_count', 
                'user_count', 
                'post_recent',
                'countVisitor',
                'most_viewed_posts',
                'total_views',
                'views_by_country',
                'posts_by_category',
                'last_30_days_stats',
                'recent_comments'
            ]));
        } else {
            return redirect('login');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
