<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

class QuizController extends Controller
{
    /* ====================== Site public ====================== */

    public function liste()
    {
        $quizzes = Quiz::enLigne()->withCount('questions')->latest()->get();

        return view('site.pages.quiz.index', compact('quizzes'));
    }

    public function jouer(string $slug)
    {
        $quiz = Quiz::enLigne()->where('slug', $slug)
            ->with(['questions.post:id,title,slug,published'])
            ->firstOrFail();

        $autres = Quiz::enLigne()->where('id', '!=', $quiz->id)->withCount('questions')->latest()->take(3)->get();

        return view('site.pages.quiz.show', compact('quiz', 'autres'));
    }

    /** Fin de partie : enregistre le score (une fois par quiz et par session) */
    public function score(Request $request, Quiz $quiz)
    {
        $data = $request->validate(['score' => 'required|integer|min:0|max:100']);
        $max  = $quiz->questions()->count();

        if ($quiz->actif && !$request->session()->has("quiz_joue.{$quiz->id}")) {
            $request->session()->put("quiz_joue.{$quiz->id}", true);
            $quiz->increment('participations');
            $quiz->increment('score_total', min($data['score'], $max));
        }

        $quiz->refresh();

        return response()->json(['moyenne' => $quiz->setAttribute('questions_count', $max)->moyenne]);
    }

    /* ====================== Administration ====================== */

    public function index()
    {
        $quizzes = Quiz::withCount('questions')->orderByDesc('actif')->latest()->get();

        return view('admin.pages.quiz.index', compact('quizzes'));
    }

    public function create()
    {
        return view('admin.pages.quiz.form', ['quiz' => new Quiz(['actif' => true]), 'articles' => $this->articles()]);
    }

    public function store(Request $request)
    {
        $quiz = DB::transaction(function () use ($request) {
            $quiz = Quiz::create($this->validated($request));
            $this->syncQuestions($request, $quiz);
            return $quiz;
        });

        Alert::toast('Quiz créé', 'success');
        return redirect()->route('quiz.edit', $quiz);
    }

    public function edit(Quiz $quiz)
    {
        $quiz->load('questions');

        return view('admin.pages.quiz.form', ['quiz' => $quiz, 'articles' => $this->articles()]);
    }

    public function update(Request $request, Quiz $quiz)
    {
        DB::transaction(function () use ($request, $quiz) {
            $quiz->update($this->validated($request));
            $this->syncQuestions($request, $quiz);
        });

        Alert::toast('Quiz enregistré', 'success');
        return redirect()->route('quiz.edit', $quiz);
    }

    public function toggle(Quiz $quiz)
    {
        $quiz->update(['actif' => !$quiz->actif]);

        Alert::toast($quiz->actif ? 'Quiz en ligne' : 'Quiz retiré du site', 'info');
        return back();
    }

    public function destroy(Quiz $quiz)
    {
        $quiz->delete();

        Alert::toast('Quiz supprimé', 'success');
        return redirect()->route('quiz.index');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'titre'                    => 'required|string|max:150',
            'intro'                    => 'nullable|string|max:500',
            'questions'                => 'required|array|min:1|max:30',
            'questions.*.affirmation'  => 'required|string|max:300',
            'questions.*.reponse'      => 'required|in:0,1',
            'questions.*.explication'  => 'required|string|max:1000',
            'questions.*.post_id'      => 'nullable|exists:posts,id',
        ], [
            'titre.required'                   => 'Donnez un titre au quiz.',
            'questions.required'               => 'Ajoutez au moins une affirmation.',
            'questions.*.affirmation.required' => 'Chaque affirmation doit être remplie.',
            'questions.*.reponse.required'     => 'Indiquez si chaque affirmation est vraie ou fausse.',
            'questions.*.explication.required' => 'Ajoutez une explication à chaque affirmation : c\'est elle qui fait apprendre.',
        ]);

        return [
            'titre' => $data['titre'],
            'intro' => $data['intro'] ?? null,
            'actif' => $request->boolean('actif'),
        ];
    }

    /** Remplace les questions par celles du formulaire, dans l'ordre affiché */
    private function syncQuestions(Request $request, Quiz $quiz): void
    {
        $quiz->questions()->delete();

        foreach (array_values($request->input('questions', [])) as $i => $q) {
            QuizQuestion::create([
                'quiz_id'     => $quiz->id,
                'affirmation' => trim($q['affirmation']),
                'reponse'     => (bool) $q['reponse'],
                'explication' => trim($q['explication']),
                'post_id'     => ($q['post_id'] ?? null) ?: null,
                'ordre'       => $i,
            ]);
        }
    }

    private function articles()
    {
        return Post::where('published', 'public')->whereNotNull('title')->orderBy('title')->get(['id', 'title']);
    }
}
