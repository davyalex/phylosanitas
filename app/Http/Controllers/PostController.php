<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\OptionSondage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class PostController extends Controller
{
    public function index()
    {
        try {
            $category_sondage = Category::whereTitle('sondage')->first();

            if (!$category_sondage) {
                return view('admin.pages.post.index', ['post' => collect(), 'sondage' => collect()]);
            }

            $category_filter = request('category_filter');

            $sondage = Post::with(['category', 'media', 'user'])
                ->withCount('commentaires')
                ->withViewsCount()
                ->where('category_id', $category_sondage->id)
                ->orderBy('created_at', 'desc')
                ->get();

            $post = Post::with(['category', 'media', 'user'])
                ->withCount('commentaires')
                ->withViewsCount()
                ->when($category_filter, fn($q) => $q->where('category_id', $category_filter))
                ->where('category_id', '!=', $category_sondage->id)
                ->orderBy('created_at', 'desc')
                ->get();

            // Aperçu de la une de l'accueil : articles mis à la une, puis complément automatique
            $une = Post::with(['category', 'media'])->withViewsCount()->aLaUne()->take(Post::UNE_PLACES)->get();
            $complement = Post::with(['category', 'media'])
                ->where('published', 'public')
                ->where('category_id', '!=', $category_sondage->id)
                ->whereNotIn('id', $une->pluck('id'))
                ->when(Category::where('slug', 'actualites')->value('id'), fn($q, $id) => $q->where('category_id', '!=', $id))
                ->latest()
                ->take(max(0, Post::UNE_PLACES - $une->count()))
                ->get();

            return view('admin.pages.post.index', compact('post', 'sondage', 'une', 'complement'));
        } catch (\Throwable $e) {
            Alert::error('Erreur', 'Impossible de charger les articles.');
            return redirect()->route('dashboard');
        }
    }

    public function published($id)
    {
        $post = Post::findOrFail($id);
        $nouveauStatut = $post->published === 'prive' ? 'public' : 'prive';
        $post->update(['published' => $nouveauStatut]);

        Alert::success('Statut modifié avec succès');
        return back();
    }

    public function actualite_une(Request $request)
    {
        $request->validate([
            'actualite_une' => 'required',
            'actualite'     => 'required|exists:posts,id',
        ]);

        $post = Post::with('category')->findOrFail($request->actualite);
        $on   = (bool) $request->actualite_une;
        $sortants = $post->mettreALaUne($on);
        $this->flushUneCaches();

        $titre = Str::limit($post->title ?: strip_tags($post->description), 60);
        if (!$on) {
            Alert::toast("« $titre » a été retiré de la une", 'info');
        } elseif ($post->category && $post->category->slug === 'actualites') {
            Alert::toast("« $titre » s'affiche dans le widget Actualités", 'success');
        } else {
            $msg = "« $titre » est maintenant l'article principal de la une";
            if ($sortants->isNotEmpty()) {
                $msg .= ' · « ' . Str::limit($sortants->first()->title, 40) . ' » a quitté la une';
            }
            Alert::toast($msg, 'success');
        }

        return back();
    }

    private function flushUneCaches(): void
    {
        \Illuminate\Support\Facades\Cache::forget(\App\Providers\AppServiceProvider::CACHE_NEWS);
    }

    public function create()
    {
        $type = request('type');
        $category = Category::with('posts')
            ->when($type === 'sondage', fn($q) => $q->whereTitle('sondage'))
            ->get();

        $redacteurs = $this->redacteurs();

        return view('admin.pages.post.add', compact('category', 'redacteurs'));
    }

    public function store(Request $request)
    {
        if ($request->input('sondage') === 'sondage') {
            $request->validate([
                'description'      => 'required',
                'category'         => 'required|exists:categories,id',
                'option.*.title'   => 'required|string|max:255',
            ]);

            $post = Post::create([
                'slug'        => 'sondage-' . Str::random(6),
                'description' => $request->description,
                'category_id' => $request->category,
                'published'   => 'prive',
            ]);

            if ($request->hasFile('image')) {
                $post->addMediaFromRequest('image')->toMediaCollection('image');
            }

            foreach ($request->input('option', []) as $option) {
                OptionSondage::create([
                    'post_id' => $post->id,
                    'title'   => $option['title'],
                ]);
            }

            Alert::toast('Sondage créé avec succès', 'success');
            return redirect()->route('post', ['type' => 'sondage']);
        }

        $this->validateArticle($request);

        $post = Post::create([
            'title'            => $request->title,
            'meta_description' => $request->meta_description,
            'description'      => $request->description,
            'category_id'      => $request->category,
            'lien'             => $request->lien,
            'published'        => $request->boolean('publier') ? 'public' : 'prive',
            'actualite_une'    => 0,
            'user_id'          => $request->input('auteur') ?: Auth::id(),
            'reviewer_id'      => $request->input('reviewer') ?: null,
            'sources'          => $request->sources,
        ]);

        if ($request->hasFile('image')) {
            $post->addMediaFromRequest('image')->toMediaCollection('image');
        }

        // À la une : devient l'article principal, le plus ancien des 4 quitte la une
        if ($request->boolean('actualite_une')) {
            $post->load('category')->mettreALaUne(true);
            $this->flushUneCaches();
        }


        Alert::toast('Article créé avec succès', 'success');
        return redirect()->route('post');
    }

    public function uploadTinyMCEImage(Request $request)
    {
        if (!$request->hasFile('file')) {
            \Log::warning('TinyMCE upload: pas de fichier', ['keys' => array_keys($request->all()), 'files' => array_keys($request->allFiles())]);
            return response()->json(['error' => 'Aucun fichier fourni'], 400);
        }

        $postId = $request->input('post_id');

        if ($postId) {
            $post = Post::find($postId);
        } else {
            $post = Post::where('user_id', Auth::id())->latest()->first();
        }

        if (!$post) {
            return response()->json(['error' => 'Article introuvable'], 404);
        }

        $media = $post->addMediaFromRequest('file')
            ->toMediaCollection('tinyMceImages');

        // URL absolue pour garantir l'affichage dans TinyMCE
        return response()->json(['location' => url($media->getUrl())]);
    }


    /** Détail d'un article ou d'un sondage : statistiques, commentaires, résultats */
    public function show($id)
    {
        $post = Post::with(['category:id,title,slug', 'media', 'user:id,name', 'optionSondages'])
            ->withCount('commentaires')
            ->findOrFail($id);

        $isPoll = strtolower($post->category->title ?? '') === 'sondage';
        $views  = DB::table('views')->where('viewable_type', Post::class)->where('viewable_id', $post->id);

        $stats = [
            'total'     => (clone $views)->count(),
            'uniques'   => (clone $views)->distinct()->count('visitor'),
            'last30'    => (clone $views)->where('viewed_at', '>=', now()->subDays(30))->count(),
            'last7'     => (clone $views)->where('viewed_at', '>=', now()->subDays(7))->count(),
            'first'     => (clone $views)->min('viewed_at'),
            'last'      => (clone $views)->max('viewed_at'),
        ];

        // Vues par jour (30 jours, sans trous)
        $byDay = (clone $views)
            ->where('viewed_at', '>=', now()->subDays(29)->startOfDay())
            ->selectRaw('DATE(viewed_at) as d, COUNT(*) as n')
            ->groupBy('d')
            ->pluck('n', 'd');
        $chart = ['labels' => [], 'values' => []];
        foreach (\Carbon\CarbonPeriod::create(now()->subDays(29)->startOfDay(), now()->startOfDay()) as $day) {
            $chart['labels'][] = $day->translatedFormat('d M');
            $chart['values'][] = (int) ($byDay[$day->toDateString()] ?? 0);
        }

        $countries = (clone $views)
            ->selectRaw("COALESCE(NULLIF(country, ''), 'Inconnu') as pays, COUNT(*) as n")
            ->groupBy('pays')
            ->orderByDesc('n')
            ->limit(8)
            ->get();

        $comments = $post->commentaires()->latest()->get();

        // Résultats du sondage
        $poll = null;
        if ($isPoll) {
            $votes = DB::table('soumissions')
                ->where('post_id', $post->id)
                ->whereNull('deleted_at')
                ->selectRaw('option_sondage_id, COUNT(*) as n')
                ->groupBy('option_sondage_id')
                ->pluck('n', 'option_sondage_id');
            $total = (int) $votes->sum();
            $poll = [
                'total'   => $total,
                'options' => $post->optionSondages->map(fn($o) => [
                    'title' => $o->title,
                    'votes' => (int) ($votes[$o->id] ?? 0),
                    'pct'   => $total ? round(($votes[$o->id] ?? 0) / $total * 100, 1) : 0,
                ])->sortByDesc('votes')->values(),
            ];
        }

        $avis = \App\Models\AvisArticle::totaux($post->id);

        return view('admin.pages.post.show', compact('post', 'isPoll', 'stats', 'chart', 'countries', 'comments', 'poll', 'avis'));
    }

    /** Modération : supprimer un commentaire depuis la page détail */
    public function destroyComment($id)
    {
        $comment = \App\Models\Commentaire::findOrFail($id);
        $postId  = $comment->post_id;
        $comment->delete();

        // Le détail public est en cache : on le rafraîchit
        if ($post = Post::find($postId)) {
            \Illuminate\Support\Facades\Cache::forget("post_detail_{$post->slug}");
        }

        Alert::toast('Commentaire supprimé', 'success');
        return back();
    }

    public function edit(Post $post, $slug)
    {
        $category = Category::with('posts')->get();
        $post = Post::with(['category', 'commentaires', 'media', 'user'])
            ->whereSlug($slug)
            ->firstOrFail();

        $redacteurs = $this->redacteurs();

        return view('admin.pages.post.edit', compact('post', 'category', 'redacteurs'));
    }

    public function editSondage($id)
    {
        $category = Category::with('posts')->get();
        $post = Post::with(['category', 'commentaires', 'media', 'user', 'optionSondages'])
            ->findOrFail($id);

        $reponseSondage = $post->optionSondages;

        return view('admin.pages.sondage.edit', compact('post', 'category', 'reponseSondage'));
    }

    public function updateSondage(Request $request, $id)
    {
        $request->validate([
            'description'    => 'required',
            'category'       => 'required|exists:categories,id',
            'option.*.title' => 'required|string|max:255',
        ]);

        $post = Post::findOrFail($id);
        $post->update([
            'description' => $request->description,
            'user_id'     => Auth::id(),
        ]);

        if ($request->hasFile('image')) {
            $post->clearMediaCollection('image');
            $post->addMediaFromRequest('image')->toMediaCollection('image');
        }

        // Supprimer et recréer les options
        OptionSondage::where('post_id', $id)->delete();

        foreach ($request->input('option', []) as $option) {
            OptionSondage::create([
                'post_id' => $post->id,
                'title'   => $option['title'],
            ]);
        }

        Alert::toast('Sondage modifié avec succès', 'success');
        return redirect()->route('post', ['type' => 'sondage']);
    }

    public function update(Request $request, $id)
    {
        $this->validateArticle($request);

        $post = Post::findOrFail($id);
        $etaitALaUne = (bool) $post->actualite_une;
        $veutLaUne   = $request->has('actualite_une') ? $request->boolean('actualite_une') : $etaitALaUne;

        $post->update([
            'title'            => $request->title,
            'meta_description' => $request->meta_description,
            'description'      => $request->description,
            'category_id'      => $request->category,
            'lien'             => $request->lien,
            'published'        => $request->has('publier') ? ($request->boolean('publier') ? 'public' : 'prive') : $post->published,
            // L'auteur ne change plus à chaque modification : il est choisi dans le formulaire
            'user_id'          => $request->input('auteur') ?: ($post->user_id ?: Auth::id()),
            'reviewer_id'      => $request->input('reviewer') ?: null,
            'sources'          => $request->sources,
        ]);

        if ($request->hasFile('image')) {
            $post->clearMediaCollection('image');
            $post->addMediaFromRequest('image')->toMediaCollection('image');
        }

        // Ne change la une que si la case a changé (sinon l'ordre de la une reste intact)
        if ($veutLaUne !== $etaitALaUne) {
            $post->load('category')->mettreALaUne($veutLaUne);
            $this->flushUneCaches();
        }


        Alert::toast('Article modifié avec succès', 'success');
        return redirect()->route('post');
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->clearMediaCollection('image');
        $post->clearMediaCollection('tinyMceImages');
        $post->delete();

        Alert::toast('Article supprimé avec succès', 'success');
        return back();
    }

    /** Comptes proposés comme auteur / relecteur (médecins en premier) */
    private function redacteurs()
    {
        return \App\Models\User::orderByDesc('est_medecin')->orderBy('name')
            ->get(['id', 'name', 'titre_pro', 'est_medecin', 'profil_public']);
    }

    private function validateArticle(Request $request): void
    {
        $request->validate([
            'title'            => 'required|string|max:500',
            'meta_description' => 'nullable|string|max:170',
            'category'         => 'required|exists:categories,id',
            'lien'             => 'nullable|url',
            'image'            => 'nullable|image|max:5120',
            'auteur'           => 'nullable|exists:users,id',
            'reviewer'         => 'nullable|exists:users,id',
            'sources'          => 'nullable|string|max:3000',
        ], [
            'title.required'         => 'Le titre est obligatoire.',
            'category.required'      => 'Choisissez une catégorie.',
            'lien.url'               => 'Le lien doit être une adresse valide (https://…).',
            'image.image'            => 'Le fichier doit être une image (JPG, PNG, WebP).',
            'image.max'              => 'L\x27image ne doit pas dépasser 5 Mo.',
            'meta_description.max'   => 'Le résumé Google ne doit pas dépasser 170 caractères.',
        ]);
    }
}
