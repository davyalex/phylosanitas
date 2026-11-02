<?php

namespace App\Http\Controllers;

use App\Models\Actualite;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class ActualiteController extends Controller
{
    public function index()
    {
        $actualite = Actualite::with('media')
            ->orderBy('ordre')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.pages.actualite.index', compact('actualite'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'        => 'required|string|max:200',
            'image'        => 'required|image|max:5120',
            'sous_titre'   => 'nullable|string|max:300',
            'lien'         => 'nullable|url|max:500',
            'texte_bouton' => 'nullable|string|max:60',
        ]);

        $ordre = Actualite::max('ordre') + 1;

        $actualite = Actualite::create([
            'title'        => $request->title,
            'sous_titre'   => $request->sous_titre,
            'description'  => $request->sous_titre,
            'lien'         => $request->lien,
            'texte_bouton' => $request->texte_bouton ?: "Lire l'article",
            'actif'        => true,
            'ordre'        => $ordre,
        ]);

        $actualite->addMediaFromRequest('image')->toMediaCollection('image');

        Alert::toast('Slide ajouté avec succès', 'success');
        return back();
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title'        => 'required|string|max:200',
            'sous_titre'   => 'nullable|string|max:300',
            'lien'         => 'nullable|url|max:500',
            'texte_bouton' => 'nullable|string|max:60',
        ]);

        $actualite = Actualite::findOrFail($id);
        $actualite->update([
            'title'        => $request->title,
            'sous_titre'   => $request->sous_titre,
            'description'  => $request->sous_titre,
            'lien'         => $request->lien,
            'texte_bouton' => $request->texte_bouton ?: "Lire l'article",
        ]);

        if ($request->hasFile('image')) {
            $actualite->clearMediaCollection('image');
            $actualite->addMediaFromRequest('image')->toMediaCollection('image');
        }

        Alert::toast('Slide modifié avec succès', 'success');
        return back();
    }

    public function toggleActive($id)
    {
        $actualite = Actualite::findOrFail($id);
        $actualite->update(['actif' => !$actualite->actif]);

        Alert::toast($actualite->actif ? 'Slide activé' : 'Slide désactivé', 'info');
        return back();
    }

    public function moveUp($id)
    {
        $current = Actualite::findOrFail($id);
        $previous = Actualite::where('ordre', '<', $current->ordre)
            ->orderBy('ordre', 'desc')
            ->first();

        if ($previous) {
            $tmpOrdre = $current->ordre;
            $current->update(['ordre'   => $previous->ordre]);
            $previous->update(['ordre'  => $tmpOrdre]);
        }

        return back();
    }

    public function moveDown($id)
    {
        $current = Actualite::findOrFail($id);
        $next = Actualite::where('ordre', '>', $current->ordre)
            ->orderBy('ordre')
            ->first();

        if ($next) {
            $tmpOrdre = $current->ordre;
            $current->update(['ordre' => $next->ordre]);
            $next->update(['ordre'    => $tmpOrdre]);
        }

        return back();
    }

    public function destroy($id)
    {
        $actualite = Actualite::findOrFail($id);
        $actualite->clearMediaCollection('image');
        $actualite->delete();

        Alert::toast('Slide supprimé avec succès', 'success');
        return back();
    }
}
