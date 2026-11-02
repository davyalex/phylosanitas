<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Models\FlashInfo;
use App\Models\Publicite;
use App\Models\ContactMessage;
use App\Models\NewsletterSubscriber;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $sondageId = Category::whereRaw('LOWER(title) = ?', ['sondage'])->value('id');

        // ── Contenus ──────────────────────────────────────────────────
        $posts = Post::selectRaw("
                SUM(published = 'public') as publies,
                SUM(published <> 'public') as brouillons,
                SUM(published = 'public' AND created_at >= ?) as publies_30j
            ", [now()->subDays(30)])
            ->when($sondageId, fn($q) => $q->where('category_id', '!=', $sondageId))
            ->first();

        $content = [
            'publies'     => (int) $posts->publies,
            'brouillons'  => (int) $posts->brouillons,
            'publies_30j' => (int) $posts->publies_30j,
            'sondages'    => $sondageId ? Post::where('category_id', $sondageId)->count() : 0,
            'categories'  => Category::count(),
            'commentaires'=> DB::table('commentaires')->whereNull('deleted_at')->count(),
        ];

        // ── Audience (compteur du site, 30 jours sans trous) ─────────
        $from   = now()->subDays(29)->startOfDay();
        $daily  = $this->safe(fn() => DB::table('site_visit_stats')->where('date', '>=', $from->toDateString())->get()->keyBy(fn($r) => (string) $r->date), collect());
        $labels = $visits = $pages = [];
        foreach (CarbonPeriod::create($from, now()->startOfDay()) as $day) {
            $key      = $day->toDateString();
            $labels[] = $day->translatedFormat('d M');
            $visits[] = (int) ($daily[$key]->visites ?? 0);
            $pages[]  = (int) ($daily[$key]->pages_vues ?? 0);
        }
        $prev = $this->safe(fn() => (int) DB::table('site_visit_stats')
            ->whereBetween('date', [now()->subDays(59)->toDateString(), now()->subDays(30)->toDateString()])
            ->sum('visites'), 0);

        $audience = [
            'labels'      => $labels,
            'visits'      => $visits,
            'pages'       => $pages,
            'today'       => end($visits) ?: 0,
            'visits_30'   => array_sum($visits),
            'pages_30'    => array_sum($pages),
            'trend'       => $prev > 0 ? round((array_sum($visits) - $prev) / $prev * 100) : null,
            'total'       => $this->safe(fn() => (int) DB::table('site_visit_stats')->sum('visites'), 0),
            'article_views' => DB::table('views')->where('viewable_type', Post::class)->count(),
        ];

        // ── Top articles (1 requête groupée) ──────────────────────────
        $topPostIds = DB::table('views')
            ->select('viewable_id', DB::raw('count(*) as views_count'))
            ->where('viewable_type', Post::class)
            ->groupBy('viewable_id')
            ->orderByDesc('views_count')
            ->limit(6)
            ->pluck('views_count', 'viewable_id');

        $most_viewed_posts = Post::with([
                'category:id,title,slug',
                'media' => fn($q) => $q->where('collection_name', 'image'),
            ])
            ->select('id', 'title', 'slug', 'category_id', 'description')
            ->whereIn('id', $topPostIds->keys())
            ->get()
            ->sortByDesc(fn($p) => $topPostIds->get($p->id, 0))
            ->values();
        $most_viewed_posts->each(fn($p) => $p->setAttribute('views_total', $topPostIds->get($p->id, 0)));

        // ── Derniers articles (avec brouillons) ───────────────────────
        $post_recent = Post::with([
                'category:id,title,slug',
                'user:id,name',
                'media' => fn($q) => $q->where('collection_name', 'image'),
            ])
            ->select('id', 'title', 'slug', 'category_id', 'user_id', 'published', 'created_at')
            ->withCount('commentaires')
            ->when($sondageId, fn($q) => $q->where('category_id', '!=', $sondageId))
            ->latest()
            ->take(6)
            ->get();

        // ── Catégories ────────────────────────────────────────────────
        $posts_by_category = Category::withCount(['posts' => fn($q) => $q->where('published', 'public')])
            ->orderByDesc('posts_count')
            ->get();

        // ── Interactions ──────────────────────────────────────────────
        $recent_comments = DB::table('commentaires')
            ->join('posts', 'commentaires.post_id', '=', 'posts.id')
            ->select('commentaires.user_name', 'commentaires.message', 'commentaires.created_at', 'posts.title as post_title', 'posts.slug as post_slug')
            ->whereNull('commentaires.deleted_at')
            ->orderByDesc('commentaires.created_at')
            ->take(4)
            ->get();

        $inbox = [
            'new'      => $this->safe(fn() => ContactMessage::where('status', 'new')->count(), 0),
            'latest'   => $this->safe(fn() => ContactMessage::latest()->take(3)->get(), collect()),
            'abonnes'  => $this->safe(fn() => NewsletterSubscriber::where('status', 'active')->count(), 0),
        ];

        // ── Monétisation & bandeau ────────────────────────────────────
        $ads = $this->safe(fn() => [
            'live'        => Publicite::diffusable()->count(),
            'impressions' => (int) Publicite::sum('impressions'),
            'clics'       => (int) Publicite::sum('clics'),
            'expiring'    => Publicite::diffusable()->whereNotNull('fin_at')->where('fin_at', '<=', now()->addDays(7))->get(['id', 'titre', 'fin_at']),
        ], ['live' => 0, 'impressions' => 0, 'clics' => 0, 'expiring' => collect()]);
        $flash_live = $this->safe(fn() => FlashInfo::visible()->count(), 0);

        $views_by_country = DB::table('views')
            ->select('country', DB::raw('count(*) as count'))
            ->whereNotNull('country')->where('country', '!=', '')
            ->groupBy('country')->orderByDesc('count')->limit(6)
            ->get();

        return view('admin.pages.index', compact(
            'content', 'audience', 'most_viewed_posts', 'post_recent', 'posts_by_category',
            'recent_comments', 'inbox', 'ads', 'flash_live', 'views_by_country'
        ));
    }

    /** Évite qu'une table absente (migration non jouée) casse le tableau de bord */
    private function safe(callable $fn, $default)
    {
        try { return $fn(); } catch (\Throwable) { return $default; }
    }
}
