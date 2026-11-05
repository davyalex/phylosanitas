<?php

namespace App\Http\Controllers;

use App\Models\Maladie;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use RealRashid\SweetAlert\Facades\Alert;

class MaladieController extends Controller
{
    public function __construct()
    {
        // Module masqué (config/sante.php). Vérifié à la requête, pas à la construction (artisan route:list).
        $this->middleware(function ($request, $next) {
            abort_unless(config('sante.modules.maladies'), 404);
            return $next($request);
        });
    }

    /* ====================== Site public ====================== */

    public function liste()
    {
        $maladies = Maladie::publiees()->orderBy('nom')->get(['id', 'nom', 'slug', 'autres_noms', 'resume']);
        $parLettre = $maladies->groupBy(fn($m) => $m->lettre)->sortKeys();

        return view('site.pages.maladies.index', compact('maladies', 'parLettre'));
    }

    public function fiche(string $slug)
    {
        $maladie = Maladie::publiees()->with('reviewer')->where('slug', $slug)->firstOrFail();

        // Articles liés : titre contenant un des mots-clés de la fiche (cache 1 h)
        $articles = Cache::remember("maladie_articles_{$maladie->id}_" . $maladie->updated_at?->timestamp, 3600, function () use ($maladie) {
            $mots = $maladie->motsCles();
            if (!$mots) return collect();

            return Post::with(['category:id,title,slug', 'media' => fn($q) => $q->where('collection_name', 'image')])
                ->select('id', 'title', 'slug', 'description', 'category_id', 'created_at')
                ->withCount('commentaires')
                ->withViewsCount()
                ->where('published', 'public')
                ->where(function ($q) use ($mots) {
                    foreach ($mots as $m) $q->orWhere('title', 'LIKE', '%' . $m . '%');
                })
                ->latest()
                ->take(3)
                ->get();
        });

        $voisines = Maladie::publiees()->where('id', '!=', $maladie->id)->inRandomOrder()->take(6)->get(['nom', 'slug']);

        return view('site.pages.maladies.show', compact('maladie', 'articles', 'voisines'));
    }

    /* ====================== Administration ====================== */

    public function index(Request $request)
    {
        $maladies = Maladie::with('reviewer:id,name')
            ->when($request->filled('q'), fn($q) => $q->where('nom', 'LIKE', '%' . $request->q . '%'))
            ->orderBy('nom')
            ->get();

        return view('admin.pages.maladie.index', compact('maladies'));
    }

    public function create()
    {
        return view('admin.pages.maladie.form', ['maladie' => new Maladie(['publie' => false]), 'medecins' => $this->medecins()]);
    }

    public function store(Request $request)
    {
        Maladie::create($this->validated($request));

        Alert::toast('Fiche créée', 'success');
        return redirect()->route('maladie.index');
    }

    public function edit(Maladie $maladie)
    {
        return view('admin.pages.maladie.form', ['maladie' => $maladie, 'medecins' => $this->medecins()]);
    }

    public function update(Request $request, Maladie $maladie)
    {
        $maladie->update($this->validated($request));

        Alert::toast('Fiche enregistrée', 'success');
        return redirect()->route('maladie.index');
    }

    public function toggle(Maladie $maladie)
    {
        $maladie->update(['publie' => !$maladie->publie]);

        Alert::toast($maladie->publie ? 'Fiche publiée' : 'Fiche repassée en brouillon', 'info');
        return back();
    }

    public function destroy(Maladie $maladie)
    {
        $maladie->delete();

        Alert::toast('Fiche supprimée', 'success');
        return back();
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'nom'             => 'required|string|max:150',
            'autres_noms'     => 'nullable|string|max:255',
            'resume'          => 'required|string|max:800',
            'symptomes'       => 'nullable|string|max:3000',
            'causes'          => 'nullable|string|max:3000',
            'prevention'      => 'nullable|string|max:3000',
            'traitement'      => 'nullable|string|max:3000',
            'quand_consulter' => 'nullable|string|max:3000',
            'mots_cles'       => 'nullable|string|max:255',
            'reviewer_id'     => 'nullable|exists:users,id',
        ], [
            'nom.required'    => 'Indiquez le nom de la maladie.',
            'resume.required' => 'Ajoutez un court résumé (2 à 3 phrases).',
        ]);

        $data['publie'] = $request->boolean('publie');

        return $data;
    }

    private function medecins()
    {
        return User::orderByDesc('est_medecin')->orderBy('name')->get(['id', 'name', 'titre_pro', 'est_medecin']);
    }
}
