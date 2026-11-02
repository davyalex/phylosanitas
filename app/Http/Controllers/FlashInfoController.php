<?php

namespace App\Http\Controllers;

use App\Models\FlashInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class FlashInfoController extends Controller
{
    public function index()
    {
        $flashes = FlashInfo::orderByDesc('actif')
            ->orderBy('ordre')
            ->latest()
            ->get();

        $stats = [
            'live'      => $flashes->where('statut', 'live')->count(),
            'scheduled' => $flashes->where('statut', 'scheduled')->count(),
            'expired'   => $flashes->where('statut', 'expired')->count(),
        ];

        return view('admin.pages.flash.index', compact('flashes', 'stats'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['user_id'] = Auth::id();
        $data['ordre']   = (int) FlashInfo::max('ordre') + 1;

        FlashInfo::create($data);

        Alert::toast('Info flash publiée', 'success');
        return redirect()->route('flash.index');
    }

    public function update(Request $request, FlashInfo $flash)
    {
        $flash->update($this->validated($request));

        Alert::toast('Info flash modifiée', 'success');
        return redirect()->route('flash.index');
    }

    public function toggle(FlashInfo $flash)
    {
        $flash->update(['actif' => !$flash->actif]);

        Alert::toast($flash->actif ? 'Info flash activée' : 'Info flash désactivée', 'info');
        return back();
    }

    public function destroy(FlashInfo $flash)
    {
        $flash->delete();

        Alert::toast('Info flash supprimée', 'success');
        return back();
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'message'  => 'required|string|max:255',
            'lien'     => 'nullable|url|max:500',
            'niveau'   => 'required|in:' . implode(',', array_keys(FlashInfo::NIVEAUX)),
            'debut_at' => 'nullable|date',
            'fin_at'   => 'nullable|date|after_or_equal:debut_at',
        ], [
            'message.required'      => 'Le message est obligatoire.',
            'message.max'           => 'Le message ne doit pas dépasser 255 caractères.',
            'lien.url'              => 'Le lien doit être une adresse valide (https://…).',
            'fin_at.after_or_equal' => 'La date de fin doit être après la date de début.',
        ]);

        $data['actif'] = $request->boolean('actif', true);

        return $data;
    }
}
