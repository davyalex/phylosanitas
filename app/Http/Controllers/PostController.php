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

            $sondage = Post::with(['category', 'commentaires', 'media', 'user'])
                ->where('category_id', $category_sondage->id)
                ->orderBy('created_at', 'desc')
                ->get();

            $post = Post::with(['category', 'commentaires', 'media', 'user', 'views'])
                ->when($category_filter, fn($q) => $q->where('category_id', $category_filter))
                ->where('category_id', '!=', $category_sondage->id)
                ->orderBy('created_at', 'desc')
                ->get();

            return view('admin.pages.post.index', compact('post', 'sondage'));
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

        Post::whereId($request->actualite)->update(['actualite_une' => $request->actualite_une]);

        Alert::success('Statut modifié avec succès');
        return back();
    }

    public function create()
    {
        $type = request('type');
        $category = Category::with('posts')
            ->when($type === 'sondage', fn($q) => $q->whereTitle('sondage'))
            ->get();

        return view('admin.pages.post.add', compact('category'));
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

        $request->validate([
            'title'    => 'required|string|max:500',
            'category' => 'required|exists:categories,id',
            'lien'     => 'nullable|url',
        ]);

        $post = Post::create([
            'title'       => $request->title,
            'description' => $request->description,
            'category_id' => $request->category,
            'lien'        => $request->lien,
            'published'   => 'prive',
            'user_id'     => Auth::id(),
        ]);

        if ($request->hasFile('image')) {
            $post->addMediaFromRequest('image')->toMediaCollection('image');
        }

        Alert::toast('Article créé avec succès', 'success');
        return redirect()->route('post');
    }

    public function uploadTinyMCEImage(Request $request)
    {
        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'Aucun fichier fourni'], 400);
        }

        $postId = $request->input('post_id');
        $post = $postId
            ? Post::find($postId)
            : Post::where('user_id', Auth::id())->latest()->first();

        if (!$post) {
            return response()->json(['error' => 'Article introuvable'], 404);
        }

        $media = $post->addMediaFromRequest('file')
            ->toMediaCollection('tinyMceImages');

        return response()->json(['location' => $media->getUrl()]);
    }

    public function edit(Post $post, $slug)
    {
        $category = Category::with('posts')->get();
        $post = Post::with(['category', 'commentaires', 'media', 'user'])
            ->whereSlug($slug)
            ->firstOrFail();

        return view('admin.pages.post.edit', compact('post', 'category'));
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
        $request->validate([
            'title'    => 'required|string|max:500',
            'category' => 'required|exists:categories,id',
            'lien'     => 'nullable|url',
        ]);

        $post = Post::findOrFail($id);
        $post->update([
            'title'       => $request->title,
            'description' => $request->description,
            'category_id' => $request->category,
            'lien'        => $request->lien,
            'user_id'     => Auth::id(),
        ]);

        if ($request->hasFile('image')) {
            $post->clearMediaCollection('image');
            $post->addMediaFromRequest('image')->toMediaCollection('image');
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
}
