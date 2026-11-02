<?php

namespace App\Http\Controllers;

use App\Models\Publicite;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class PubliciteController extends Controller
{
    public function index(Request $request)
    {
        $emplacement = $request->query('emplacement');

        $publicites = Publicite::with(['media' => fn($q) => $q->where('collection_name', 'image')])
            ->when($emplacement, fn($q) => $q->where('emplacement', $emplacement))
            ->orderByDesc('actif')
            ->latest()
            ->get();

        $all = Publicite::get(['id', 'emplacement', 'actif', 'debut_at', 'fin_at', 'impressions', 'clics']);
        $stats = [
            'live'        => $all->where('statut', 'live')->count(),
            'impressions' => $all->sum('impressions'),
            'clics'       => $all->sum('clics'),
        ];
        $stats['ctr'] = $stats['impressions'] > 0 ? round($stats['clics'] / $stats['impressions'] * 100, 2) : 0;

        $parEmplacement = $all->where('statut', 'live')->countBy('emplacement');

        return view('admin.pages.publicite.index', compact('publicites', 'stats', 'parEmplacement', 'emplacement'));
    }

    public function create()
    {
        return view('admin.pages.publicite.form', ['pub' => new Publicite(['type' => 'image', 'actif' => true, 'priorite' => 1])]);
    }

    public function store(Request $request)
    {
        $pub = Publicite::create($this->validated($request));
        $this->syncImage($request, $pub);

        Alert::toast('Publicité créée', 'success');
        return redirect()->route('publicite.index');
    }

    public function edit(Publicite $publicite)
    {
        return view('admin.pages.publicite.form', ['pub' => $publicite]);
    }

    public function update(Request $request, Publicite $publicite)
    {
        $publicite->update($this->validated($request, $publicite));
        $this->syncImage($request, $publicite);

        Alert::toast('Publicité modifiée', 'success');
        return redirect()->route('publicite.index');
    }

    public function toggle(Publicite $publicite)
    {
        $publicite->update(['actif' => !$publicite->actif]);

        Alert::toast($publicite->actif ? 'Publicité activée' : 'Publicité mise en pause', 'info');
        return back();
    }

    public function resetStats(Publicite $publicite)
    {
        $publicite->forceFill(['impressions' => 0, 'clics' => 0])->save();

        Alert::toast('Statistiques remises à zéro', 'info');
        return back();
    }

    public function destroy(Publicite $publicite)
    {
        $publicite->clearMediaCollection('image');
        $publicite->delete();

        Alert::toast('Publicité supprimée', 'success');
        return back();
    }

    /** Route publique : compte le clic puis redirige vers l'annonceur */
    public function click(Publicite $publicite)
    {
        abort_unless($publicite->lien, 404);

        $publicite->increment('clics');

        return redirect()->away($publicite->lien);
    }

    private function validated(Request $request, ?Publicite $pub = null): array
    {
        $hasImage = $pub && $pub->getFirstMedia('image');

        $data = $request->validate([
            'titre'       => 'required|string|max:150',
            'annonceur'   => 'nullable|string|max:150',
            'emplacement' => 'required|in:' . implode(',', array_keys(Publicite::EMPLACEMENTS)),
            'type'        => 'required|in:image,code',
            'lien'        => 'nullable|url|max:500|required_if:type,image',
            'code'        => 'nullable|string|required_if:type,code',
            'image'       => ($request->input('type') === 'image' && !$hasImage ? 'required' : 'nullable') . '|image|max:4096',
            'debut_at'    => 'nullable|date',
            'fin_at'      => 'nullable|date|after_or_equal:debut_at',
            'priorite'    => 'nullable|integer|min:1|max:10',
        ], [
            'titre.required'        => 'Donnez un nom à cette publicité.',
            'lien.required_if'      => 'Le lien de destination est obligatoire pour une bannière image.',
            'lien.url'              => 'Le lien doit être une adresse valide (https://…).',
            'code.required_if'      => 'Collez le code de l\'annonce (AdSense ou HTML).',
            'image.required'        => 'Ajoutez l\'image de la bannière.',
            'image.image'           => 'Le fichier doit être une image (JPG, PNG, WebP, GIF).',
            'image.max'             => 'L\'image ne doit pas dépasser 4 Mo.',
            'fin_at.after_or_equal' => 'La date de fin doit être après la date de début.',
        ]);

        unset($data['image']);
        // La date de fin inclut toute la journée choisie
        $data['fin_at'] = !empty($data['fin_at']) ? \Carbon\Carbon::parse($data['fin_at'])->endOfDay() : null;
        $data['debut_at'] = !empty($data['debut_at']) ? \Carbon\Carbon::parse($data['debut_at'])->startOfDay() : null;
        if ($data['type'] === 'image') $data['code'] = null;
        $data['actif']    = $request->boolean('actif');
        $data['priorite'] = $data['priorite'] ?? 1;

        return $data;
    }

    private function syncImage(Request $request, Publicite $pub): void
    {
        if ($request->hasFile('image')) {
            $pub->clearMediaCollection('image');
            $pub->addMediaFromRequest('image')->toMediaCollection('image');
            $pub->touch(); // vide le cache public
        }
    }
}
