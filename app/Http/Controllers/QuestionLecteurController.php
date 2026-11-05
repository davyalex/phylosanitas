<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\QuestionLecteur;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

/** « Le médecin répond » */
class QuestionLecteurController extends Controller
{
    public function __construct()
    {
        // Module masqué (config/sante.php) en attendant le forum. Vérifié à la requête, pas à la construction (artisan route:list).
        $this->middleware(function ($request, $next) {
            abort_unless(config('sante.modules.questions'), 404);
            return $next($request);
        });
    }

    /* ====================== Site public ====================== */

    public function liste(Request $request)
    {
        $questions = QuestionLecteur::publiees()
            ->with(['category:id,title,slug', 'medecin:id,name,slug,titre_pro,profil_public,photo,est_medecin'])
            ->when($request->filled('q'), fn($q) => $q->where(fn($w) => $w
                ->where('titre', 'LIKE', '%' . $request->q . '%')
                ->orWhere('question', 'LIKE', '%' . $request->q . '%')))
            ->latest('repondu_at')
            ->paginate(10)
            ->withQueryString();

        return view('site.pages.questions.index', compact('questions'));
    }

    public function voir(string $slug)
    {
        $question = QuestionLecteur::publiees()
            ->with(['category:id,title,slug', 'medecin'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Compteur de lectures, une fois par session
        if (!session()->has("question_vue.{$question->id}")) {
            session()->put("question_vue.{$question->id}", true);
            $question->increment('vues');
        }

        $autres = QuestionLecteur::publiees()->where('id', '!=', $question->id)
            ->when($question->category_id, fn($q) => $q->orderByRaw('category_id = ? DESC', [$question->category_id]))
            ->latest('repondu_at')->take(4)->get(['id', 'titre', 'question', 'slug', 'repondu_at']);

        return view('site.pages.questions.show', compact('question', 'autres'));
    }

    public function envoyer(Request $request)
    {
        $data = $request->validate([
            'nom'         => 'required|string|max:100',
            'email'       => 'nullable|email|max:150',
            'tranche_age' => 'nullable|in:' . implode(',', QuestionLecteur::TRANCHES_AGE),
            'question'    => 'required|string|min:20|max:2000',
            'accord'      => 'accepted',
        ], [
            'nom.required'      => 'Indiquez un prénom ou un pseudo.',
            'question.required' => 'Écrivez votre question.',
            'question.min'      => 'Décrivez un peu plus votre question (au moins 20 caractères).',
            'accord.accepted'   => 'Cochez la case pour accepter la publication anonyme.',
        ]);

        unset($data['accord']);
        QuestionLecteur::create($data + ['statut' => 'nouvelle']);

        return redirect()->route('questions.index')->with('question_envoyee', true);
    }

    /* ====================== Administration ====================== */

    public function index(Request $request)
    {
        $statut = in_array($request->statut, ['nouvelle', 'publiee', 'rejetee'], true) ? $request->statut : 'nouvelle';

        $questions = QuestionLecteur::with('medecin:id,name')
            ->where('statut', $statut)
            ->latest($statut === 'publiee' ? 'repondu_at' : 'created_at')
            ->paginate(20)
            ->withQueryString();

        $compteurs = QuestionLecteur::selectRaw('statut, count(*) as n')->groupBy('statut')->pluck('n', 'statut');

        return view('admin.pages.question.index', compact('questions', 'statut', 'compteurs'));
    }

    public function edit(QuestionLecteur $question)
    {
        $categories = Category::whereNotIn('slug', ['sondage', 'actualites'])->orderBy('title')->get(['id', 'title']);
        $medecins   = User::orderByDesc('est_medecin')->orderBy('name')->get(['id', 'name', 'titre_pro', 'est_medecin']);

        return view('admin.pages.question.edit', compact('question', 'categories', 'medecins'));
    }

    public function update(Request $request, QuestionLecteur $question)
    {
        $publier = $request->input('action') === 'publier';

        $data = $request->validate([
            'titre'       => ($publier ? 'required' : 'nullable') . '|string|max:200',
            'question'    => 'required|string|max:2000',
            'reponse'     => ($publier ? 'required' : 'nullable') . '|string|max:10000',
            'category_id' => 'nullable|exists:categories,id',
            'repondu_par' => 'nullable|exists:users,id',
        ], [
            'titre.required'   => 'Donnez un titre court à la question avant de la publier (ex. « Peut-on faire du sport avec de l\'hypertension ? »).',
            'reponse.required' => 'Écrivez la réponse avant de publier.',
        ]);

        $question->fill($data);
        $question->repondu_par = $data['repondu_par'] ?? $question->repondu_par ?? Auth::id();

        if ($publier) {
            $question->statut = 'publiee';
            $question->repondu_at ??= now();
            $question->slug ??= $question->genererSlug();
        }

        $question->save();

        if ($publier) {
            Alert::toast('Réponse publiée sur le site', 'success');
            return redirect()->route('question.index', ['statut' => 'nouvelle']);
        }

        Alert::toast('Brouillon enregistré', 'success');
        return back();
    }

    public function rejeter(QuestionLecteur $question)
    {
        $question->update(['statut' => 'rejetee']);

        Alert::toast('Question classée sans suite', 'info');
        return redirect()->route('question.index');
    }

    public function depublier(QuestionLecteur $question)
    {
        $question->update(['statut' => 'nouvelle']);

        Alert::toast('Question retirée du site', 'info');
        return back();
    }

    public function destroy(QuestionLecteur $question)
    {
        $question->delete();

        Alert::toast('Question supprimée', 'success');
        return redirect()->route('question.index', ['statut' => $question->statut]);
    }
}
