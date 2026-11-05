<?php

namespace App\Http\Controllers;

use App\Models\JourneeSante;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class JourneeSanteController extends Controller
{
    /** Page publique : calendrier des journées de santé à venir */
    public function calendrier()
    {
        $groupes = JourneeSante::calendrierAnnee();
        $anneeAVenir = now()->year;

        // Fin d'année (plus rien à venir) : on montre le début de l'année suivante
        if ($groupes['a_venir']->isEmpty()) {
            $suivante = JourneeSante::calendrierAnnee(now()->addYear()->startOfYear());
            $today = now()->startOfDay();
            $groupes['a_venir'] = $suivante['en_cours']->concat($suivante['a_venir'])->take(4)
                ->each(function ($j) use ($today) {
                    $j->etat = 'a_venir';
                    $j->dans = (int) $today->diffInDays($j->debut);
                })->values();
            $anneeAVenir = now()->year + 1;
        }

        // « À venir » regroupé par mois pour une lecture plus claire
        $aVenirParMois = $groupes['a_venir']->groupBy(fn($j) => $j->debut->format('Y-m'));

        return view('site.pages.calendrier', [
            'enCours'       => $groupes['en_cours'],
            'aVenirParMois' => $aVenirParMois,
            'passees'       => $groupes['passees'],
            'total'         => $groupes['en_cours']->count() + $groupes['a_venir']->count() + $groupes['passees']->count(),
            'annee'         => now()->year,
            'anneeAVenir'   => $anneeAVenir,
        ]);
    }

    /* ====================== Administration ====================== */

    public function index()
    {
        $journees = JourneeSante::orderBy('mois')->orderByRaw('COALESCE(jour, 0)')->get();
        $bandeau  = JourneeSante::pourBandeau();

        return view('admin.pages.journee.index', compact('journees', 'bandeau'));
    }

    public function store(Request $request)
    {
        JourneeSante::create($this->validated($request));

        Alert::toast('Journée ajoutée', 'success');
        return back();
    }

    public function update(Request $request, JourneeSante $journee)
    {
        $journee->update($this->validated($request));

        Alert::toast('Journée modifiée', 'success');
        return back();
    }

    public function toggle(JourneeSante $journee)
    {
        $journee->update(['actif' => !$journee->actif]);

        Alert::toast($journee->actif ? 'Journée activée' : 'Journée désactivée', 'info');
        return back();
    }

    public function destroy(JourneeSante $journee)
    {
        $journee->delete();

        Alert::toast('Journée supprimée', 'success');
        return back();
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'titre'         => 'required|string|max:150',
            'mois'          => 'required|integer|between:1,12',
            'jour'          => 'nullable|required_unless:tout_le_mois,1|integer|between:1,31',
            'description'   => 'nullable|string|max:400',
            'couleur'       => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'lien'          => ['nullable', 'string', 'max:500', 'regex:#^(https?://|/)#i'],
            'annonce_jours' => 'nullable|integer|between:0,30',
        ], [
            'titre.required'       => 'Indiquez le nom de la journée.',
            'jour.required_unless' => 'Indiquez le jour, ou cochez « Tout le mois ».',
            'lien.regex'     => 'Le lien doit commencer par https:// ou par / (page du site).',
        ]);

        $data['jour']          = $request->boolean('tout_le_mois') ? null : ($data['jour'] ?? null);
        $data['couleur']       = $data['couleur'] ?? '#0066cc';
        $data['annonce_jours'] = $data['annonce_jours'] ?? 7;
        $data['actif']         = $request->boolean('actif');

        return $data;
    }
}
