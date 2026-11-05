<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;

/** Auteurs et médecins relecteurs : page publique + profil public modifiable */
class AuteurController extends Controller
{
    public function voir(string $slug)
    {
        $auteur = User::where('slug', $slug)->where('profil_public', true)->firstOrFail();

        $articles = Post::with(['category:id,title,slug', 'media' => fn($q) => $q->where('collection_name', 'image')])
            ->select('id', 'title', 'slug', 'description', 'category_id', 'created_at')
            ->withCount('commentaires')
            ->withViewsCount()
            ->where('published', 'public')
            ->whereNotNull('title')
            ->where(fn($q) => $q->where('user_id', $auteur->id)->orWhere('reviewer_id', $auteur->id))
            ->latest()
            ->paginate(12);

        $nbEcrits = Post::where('published', 'public')->where('user_id', $auteur->id)->count();
        $nbRelus  = Post::where('published', 'public')->where('reviewer_id', $auteur->id)->count();

        return view('site.pages.auteur', compact('auteur', 'articles', 'nbEcrits', 'nbRelus'));
    }

    /** Admin : profil public (fonction, biographie, photo) — soi-même, ou tout compte pour un administrateur */
    public function updateProfil(Request $request, User $user)
    {
        abort_unless(Auth::id() === $user->id || Auth::user()->hasRole('administrateur'), 403);

        $data = $request->validate([
            'titre_pro'    => 'nullable|string|max:120',
            'bio'          => 'nullable|string|max:1500',
            'photo'        => 'nullable|image|max:3072',
        ], [
            'photo.image' => 'La photo doit être une image (JPG, PNG, WebP).',
            'photo.max'   => 'La photo ne doit pas dépasser 3 Mo.',
        ]);

        $user->titre_pro     = $data['titre_pro'] ?? null;
        $user->bio           = $data['bio'] ?? null;
        $user->est_medecin   = $request->boolean('est_medecin');
        $user->profil_public = $request->boolean('profil_public');

        if ($request->hasFile('photo')) {
            if ($user->photo) Storage::disk('public')->delete($user->photo);
            $user->photo = $request->file('photo')->store('auteurs', 'public');
        } elseif ($request->boolean('retirer_photo') && $user->photo) {
            Storage::disk('public')->delete($user->photo);
            $user->photo = null;
        }

        $user->save();

        // Les pages d'articles affichent l'auteur : on vide leur cache
        Post::where('user_id', $user->id)->orWhere('reviewer_id', $user->id)->pluck('slug')
            ->each(fn($slug) => \Illuminate\Support\Facades\Cache::forget("post_detail_{$slug}"));

        Alert::toast('Profil public enregistré', 'success');
        return back()->with('onglet', 'public');
    }
}
