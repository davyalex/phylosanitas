<?php

namespace App\Http\Controllers;

use App\Models\CarouselSlide;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class CarouselController extends Controller
{
    public function index()
    {
        $actualite = CarouselSlide::with('media')
            ->orderBy('ordre')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.pages.actualite.index', compact('actualite'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'        => 'required|string|max:250',
            'image'        => 'required|image|max:5120',
            'sous_titre'   => 'nullable|string|max:350',
            'lien'         => 'nullable|url|max:500',
            'texte_bouton' => 'nullable|string|max:60',
        ]);

        $ordre = CarouselSlide::max('ordre') + 1;

        $slide = CarouselSlide::create([
            'title'        => $request->title,
            'sous_titre'   => $request->sous_titre,
            'lien'         => $request->lien,
            'texte_bouton' => $request->texte_bouton ?: "Lire l'article",
            'actif'        => true,
            'ordre'        => $ordre,
        ]);

        $slide->addMediaFromRequest('image')->toMediaCollection('image');

        Alert::toast('Slide ajouté avec succès', 'success');
        return back();
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title'        => 'required|string|max:250',
            'sous_titre'   => 'nullable|string|max:350',
            'lien'         => 'nullable|url|max:500',
            'texte_bouton' => 'nullable|string|max:60',
        ]);

        $slide = CarouselSlide::findOrFail($id);
        $slide->update([
            'title'        => $request->title,
            'sous_titre'   => $request->sous_titre,
            'lien'         => $request->lien,
            'texte_bouton' => $request->texte_bouton ?: "Lire l'article",
        ]);

        if ($request->hasFile('image')) {
            $slide->clearMediaCollection('image');
            $slide->addMediaFromRequest('image')->toMediaCollection('image');
        }

        Alert::toast('Slide modifié avec succès', 'success');
        return back();
    }

    public function toggleActive($id)
    {
        $slide = CarouselSlide::findOrFail($id);
        $slide->update(['actif' => !$slide->actif]);

        Alert::toast($slide->actif ? 'Slide activé' : 'Slide désactivé', 'info');
        return back();
    }

    public function moveUp($id)
    {
        $current  = CarouselSlide::findOrFail($id);
        $previous = CarouselSlide::where('ordre', '<', $current->ordre)
            ->orderByDesc('ordre')->first();

        if ($previous) {
            [$current->ordre, $previous->ordre] = [$previous->ordre, $current->ordre];
            $current->save();
            $previous->save();
        }

        return back();
    }

    public function moveDown($id)
    {
        $current = CarouselSlide::findOrFail($id);
        $next    = CarouselSlide::where('ordre', '>', $current->ordre)
            ->orderBy('ordre')->first();

        if ($next) {
            [$current->ordre, $next->ordre] = [$next->ordre, $current->ordre];
            $current->save();
            $next->save();
        }

        return back();
    }

    public function destroy($id)
    {
        $slide = CarouselSlide::findOrFail($id);
        $slide->clearMediaCollection('image');
        $slide->delete();

        Alert::toast('Slide supprimé avec succès', 'success');
        return back();
    }
}
