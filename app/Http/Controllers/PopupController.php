<?php

namespace App\Http\Controllers;

use App\Models\Popup;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class PopupController extends Controller
{
    public function index()
    {
        $popups = Popup::with(['media' => fn($q) => $q->where('collection_name', 'image')])
            ->orderByDesc('actif')
            ->latest()
            ->get();

        // Celui qui s'affiche réellement sur le site
        $enLigne = Popup::forSite();

        return view('admin.pages.popup.index', compact('popups', 'enLigne'));
    }

    public function create()
    {
        return view('admin.pages.popup.form', ['popup' => new Popup([
            'actif' => true, 'pages' => 'tout', 'frequence' => 'daily', 'delai' => 3,
        ])]);
    }

    public function store(Request $request)
    {
        $popup = Popup::create($this->validated($request));
        $this->syncImage($request, $popup);

        Alert::toast('Popup créé', 'success');
        return redirect()->route('popup.index');
    }

    public function edit(Popup $popup)
    {
        return view('admin.pages.popup.form', compact('popup'));
    }

    public function update(Request $request, Popup $popup)
    {
        $popup->update($this->validated($request, $popup));
        $this->syncImage($request, $popup);

        Alert::toast('Popup modifié', 'success');
        return redirect()->route('popup.index');
    }

    public function toggle(Popup $popup)
    {
        $popup->update(['actif' => !$popup->actif]);

        Alert::toast($popup->actif ? 'Popup activé' : 'Popup mis en pause', 'info');
        return back();
    }

    public function resetStats(Popup $popup)
    {
        $popup->forceFill(['vues' => 0, 'clics' => 0])->save();

        Alert::toast('Statistiques remises à zéro', 'info');
        return back();
    }

    public function destroy(Popup $popup)
    {
        $popup->clearMediaCollection('image');
        $popup->delete();

        Alert::toast('Popup supprimé', 'success');
        return back();
    }

    /** Route publique : le popup vient d'être affiché à un visiteur */
    public function vue(Popup $popup)
    {
        $popup->increment('vues');

        return response()->noContent();
    }

    /** Route publique : clic sur le bouton du popup, compté puis redirigé */
    public function clic(Popup $popup)
    {
        abort_unless($popup->lien, 404);

        $popup->increment('clics');

        return str_starts_with($popup->lien, '/')
            ? redirect()->to($popup->lien)
            : redirect()->away($popup->lien);
    }

    private function validated(Request $request, ?Popup $popup = null): array
    {
        $hasImage = $popup && $popup->getFirstMedia('image');

        $data = $request->validate([
            'titre'        => 'required|string|max:150',
            'image'        => ($hasImage ? 'nullable' : 'required') . '|image|max:5120',
            'bouton_texte' => 'nullable|string|max:60|required_with:lien',
            'lien'         => ['nullable', 'string', 'max:500', 'regex:#^(https?://|/)#i'],
            'pages'        => 'required|in:' . implode(',', array_keys(Popup::PAGES)),
            'frequence'    => 'required|in:' . implode(',', array_keys(Popup::FREQUENCES)),
            'delai'        => 'nullable|integer|min:0|max:30',
            'debut_at'     => 'nullable|date',
            'fin_at'       => 'nullable|date|after_or_equal:debut_at',
        ], [
            'titre.required'            => 'Donnez un titre au popup (ex. « Novembre bleu 2026 »).',
            'image.required'            => 'Ajoutez l\'affiche du popup.',
            'image.image'               => 'Le fichier doit être une image (JPG, PNG, WebP).',
            'image.max'                 => 'L\'image ne doit pas dépasser 5 Mo.',
            'bouton_texte.required_with'=> 'Indiquez le texte du bouton (ex. « En savoir plus »).',
            'lien.regex'                => 'Le lien doit commencer par https:// ou par / (page du site).',
            'fin_at.after_or_equal'     => 'La date de fin doit être après la date de début.',
        ]);

        unset($data['image']);
        $data['texte'] = null;
        $data['debut_at'] = !empty($data['debut_at']) ? \Carbon\Carbon::parse($data['debut_at'])->startOfDay() : null;
        $data['fin_at']   = !empty($data['fin_at']) ? \Carbon\Carbon::parse($data['fin_at'])->endOfDay() : null;
        $data['delai']    = $data['delai'] ?? 3;
        $data['actif']    = $request->boolean('actif');
        if (blank($data['lien'] ?? null)) $data['bouton_texte'] = null;

        return $data;
    }

    private function syncImage(Request $request, Popup $popup): void
    {
        if ($request->hasFile('image')) {
            $popup->clearMediaCollection('image');
            $popup->addMediaFromRequest('image')->toMediaCollection('image');
            $popup->touch(); // vide le cache public
        }
    }
}
