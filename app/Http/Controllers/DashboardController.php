<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $post_count     = Post::count();
        $category_count = Category::count();
        $user_count     = User::count();
        $countVisitor   = DB::table('visits')->distinct('primary_key')->count('primary_key');

        // ── Top 10 articles les plus visités ──────────────────────────
        // 1 seule requête SQL groupée au lieu de views($post)->count() × N
        $topPostIds = DB::table('views')
            ->select('viewable_id', DB::raw('count(*) as views_count'))
            ->where('viewable_type', Post::class)
            ->groupBy('viewable_id')
            ->orderByDesc('views_count')
            ->limit(10)
            ->pluck('views_count', 'viewable_id');

        $most_viewed_posts = Post::with([
                'category:id,title,slug',
                'media' => fn($q) => $q->where('collection_name', 'image'),
            ])
            ->withCount('commentaires')
            ->select('id', 'title', 'slug', 'category_id')
            ->whereIn('id', $topPostIds->keys())
            ->get()
            ->sortByDesc(fn($p) => $topPostIds->get($p->id, 0))
            ->map(fn($post) => [
                'title'    => $post->title,
                'slug'     => $post->slug,
                'category' => $post->category->title ?? '—',
                'views'    => $topPostIds->get($post->id, 0),
                'comments' => $post->commentaires_count,
                'image'    => $post->getFirstMediaUrl('image'),
            ]);

        // ── Total vues ────────────────────────────────────────────────
        $total_views = DB::table('views')
            ->where('viewable_type', Post::class)
            ->count();

        // ── Vues par pays ─────────────────────────────────────────────
        $views_by_country = DB::table('views')
            ->select('country', DB::raw('count(*) as count'))
            ->whereNotNull('country')
            ->where('country', '!=', '')
            ->groupBy('country')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // ── 10 derniers articles publiés ──────────────────────────────
        $post_recent = Post::with([
                'category:id,title,slug',
                'user:id,name',
                'media' => fn($q) => $q->where('collection_name', 'image'),
            ])
            ->withCount('commentaires')
            ->select('id', 'title', 'slug', 'category_id', 'user_id', 'created_at')
            ->where('published', 'public')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // ── Posts par catégorie ───────────────────────────────────────
        $posts_by_category = Category::withCount([
            'posts' => fn($q) => $q->where('published', 'public'),
        ])
        ->orderByDesc('posts_count')
        ->get();

        // ── Publications des 30 derniers jours ───────────────────────
        $last_30_days_stats = DB::table('posts')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', now()->subDays(30))
            ->where('published', 'public')
            ->whereNull('deleted_at')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // ── Commentaires récents ──────────────────────────────────────
        $recent_comments = DB::table('commentaires')
            ->join('posts', 'commentaires.post_id', '=', 'posts.id')
            ->select('commentaires.user_name', 'commentaires.message', 'commentaires.created_at',
                     'posts.title as post_title', 'posts.slug as post_slug')
            ->whereNull('commentaires.deleted_at')
            ->orderByDesc('commentaires.created_at')
            ->take(5)
            ->get();

        return view('admin.pages.index', compact(
            'post_count', 'category_count', 'user_count', 'post_recent',
            'countVisitor', 'most_viewed_posts', 'total_views',
            'views_by_country', 'posts_by_category', 'last_30_days_stats', 'recent_comments'
        ));
    }
}
