<?php

namespace App\Http\Controllers;

use App\Models\Dossier;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

class DossierController extends Controller
{
    public function __construct()
    {
        // Module masqué (config/sante.php) : pages publiques et administration indisponibles.
        // Vérifié à la requête (middleware) et non à la construction, pour ne pas gêner artisan route:list / route:cache.
        $this->middleware(function ($request, $next) {
            abort_unless(config('sante.modules.dossiers'), 404);
            return $next($request);
        });
    }

    private function mediaImage(): array
    {
        return ['media' => fn($q) => $q->where('collection_name', 'image')];
    }

    /* ====================== Site public ====================== */

    public function liste()
    {
        $dossiers = Dossier::enLigne()
            ->withCount(['posts as articles_count' => fn($q) => $q->where('published', 'public')])
            ->with(['postsPublies' => fn($q) => $q->with($this->mediaImage())->select('posts.id', 'posts.title', 'posts.slug')])
            ->orderBy('ordre')->latest()
            ->get();

        return view('site.pages.dossiers.index', compact('dossiers'));
    }

    public function voir(string $slug)
    {
        $dossier = Dossier::enLigne()->where('slug', $slug)->firstOrFail();

        $articles = $dossier->postsPublies()
            ->with(['category:id,title,slug'] + $this->mediaImage())
            ->select('posts.id', 'posts.title', 'posts.slug', 'posts.description', 'posts.meta_description', 'posts.category_id', 'posts.created_at')
            ->get();

        $autres = Dossier::enLigne()->where('id', '!=', $dossier->id)
            ->withCount(['posts as articles_count' => fn($q) => $q->where('published', 'public')])
            ->orderBy('ordre')->take(3)->get();

        return view('site.pages.dossiers.show', compact('dossier', 'articles', 'autres'));
    }

    /* ====================== Administration ====================== */

    public function index()
    {
        $dossiers = Dossier::withCount('posts')->orderBy('ordre')->latest()->get();

        return view('admin.pages.dossier.index', compact('dossiers'));
    }

    public function create()
    {
        return view('admin.pages.dossier.form', ['dossier' => new Dossier(['actif' => true]), 'articles' => $this->articles(), 'choisis' => collect()]);
    }

    public function store(Request $request)
    {
        $dossier = DB::transaction(function () use ($request) {
            $dossier = Dossier::create($this->validated($request));
            $this->syncArticles($request, $dossier);
            return $dossier;
        });

        Alert::toast('Dossier créé', 'success');
        return redirect()->route('dossier.edit', $dossier);
    }

    public function edit(Dossier $dossier)
    {
        return view('admin.pages.dossier.form', [
            'dossier'  => $dossier,
            'articles' => $this->articles(),
            'choisis'  => $dossier->posts()->get(['posts.id', 'posts.title', 'posts.published']),
        ]);
    }

    public function update(Request $request, Dossier $dossier)
    {
        DB::transaction(function () use ($request, $dossier) {
            $dossier->update($this->validated($request));
            $this->syncArticles($request, $dossier);
        });

        Alert::toast('Dossier enregistré', 'success');
        return redirect()->route('dossier.edit', $dossier);
    }

    public function toggle(Dossier $dossier)
    {
        $dossier->update(['actif' => !$dossier->actif]);

        Alert::toast($dossier->actif ? 'Dossier en ligne' : 'Dossier retiré du site', 'info');
        return back();
    }

    public function destroy(Dossier $dossier)
    {
        $dossier->posts()->detach();
        $dossier->delete();

        Alert::toast('Dossier supprimé', 'success');
        return redirect()->route('dossier.index');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'titre'      => 'required|string|max:150',
            'intro'      => 'nullable|string|max:1500',
            'ordre'      => 'nullable|integer|min:0|max:999',
            'articles'   => 'required|array|min:2|max:30',
            'articles.*' => 'integer|exists:posts,id',
        ], [
            'titre.required'    => 'Donnez un titre au dossier.',
            'articles.required' => 'Ajoutez au moins deux articles au dossier.',
            'articles.min'      => 'Un dossier regroupe au moins deux articles.',
        ]);

        return [
            'titre' => $data['titre'],
            'intro' => $data['intro'] ?? null,
            'ordre' => $data['ordre'] ?? 0,
            'actif' => $request->boolean('actif'),
        ];
    }

    private function syncArticles(Request $request, Dossier $dossier): void
    {
        $sync = [];
        foreach (array_values(array_unique($request->input('articles', []))) as $i => $id) {
            $sync[(int) $id] = ['ordre' => $i];
        }
        $dossier->posts()->sync($sync);
    }

    private function articles()
    {
        $sondage = \App\Models\Category::whereTitle('sondage')->value('id');

        return Post::whereNotNull('title')
            ->when($sondage, fn($q) => $q->where('category_id', '!=', $sondage))
            ->orderByDesc('created_at')
            ->get(['id', 'title', 'published']);
    }
}
