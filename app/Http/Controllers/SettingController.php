<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;

/** Admin > Configuration : identité, coordonnées, réseaux sociaux, affichage */
class SettingController extends Controller
{
    private const TEXT_FIELDS = [
        'site_nom', 'slogan', 'description', 'adresse', 'ville', 'pays_code', 'horaires',
        'telephone_1', 'telephone_1_label', 'telephone_2', 'telephone_2_label', 'telephone_3', 'telephone_3_label',
        'email_1', 'email_1_label', 'email_2', 'email_2_label', 'email_3', 'email_3_label',
        'whatsapp', 'facebook', 'twitter', 'instagram', 'linkedin', 'youtube', 'tiktok',
        'visites_depart', 'avertissement',
    ];

    public function edit()
    {
        $s = Setting::allCached();
        $visits = \App\Http\Middleware\CountSiteVisit::totals();

        return view('admin.pages.settings.edit', compact('s', 'visits'));
    }

    public function update(Request $request)
    {
        $phone = ['nullable', 'string', 'max:30', 'regex:/^[\d\s+().\-]{6,30}$/'];
        $label = 'nullable|string|max:40';

        $data = $request->validate([
            'site_nom'          => 'required|string|max:60',
            'slogan'            => 'nullable|string|max:120',
            'description'       => 'nullable|string|max:300',
            'adresse'           => 'nullable|string|max:200',
            'ville'             => 'nullable|string|max:80',
            'pays_code'         => 'nullable|string|size:2',
            'horaires'          => 'nullable|string|max:200',
            'telephone_1'       => $phone, 'telephone_1_label' => $label,
            'telephone_2'       => $phone, 'telephone_2_label' => $label,
            'telephone_3'       => $phone, 'telephone_3_label' => $label,
            'email_1'           => 'nullable|email|max:150', 'email_1_label' => $label,
            'email_2'           => 'nullable|email|max:150', 'email_2_label' => $label,
            'email_3'           => 'nullable|email|max:150', 'email_3_label' => $label,
            'whatsapp'          => $phone,
            'facebook'          => 'nullable|url|max:300',
            'twitter'           => 'nullable|url|max:300',
            'instagram'         => 'nullable|url|max:300',
            'linkedin'          => 'nullable|url|max:300',
            'youtube'           => 'nullable|url|max:300',
            'tiktok'            => 'nullable|url|max:300',
            'visites_depart'    => 'nullable|integer|min:0|max:100000000',
            'avertissement'     => 'nullable|string|max:500',
            'logo'              => 'nullable|image|mimes:png,jpg,jpeg,webp,svg|max:2048',
        ], [
            'site_nom.required'   => 'Le nom du site est obligatoire.',
            'telephone_1.regex'   => 'Le téléphone 1 contient des caractères non autorisés.',
            'telephone_2.regex'   => 'Le téléphone 2 contient des caractères non autorisés.',
            'telephone_3.regex'   => 'Le téléphone 3 contient des caractères non autorisés.',
            'whatsapp.regex'      => 'Le numéro WhatsApp contient des caractères non autorisés.',
            'email_1.email'       => 'L\'email 1 n\'est pas une adresse valide.',
            'email_2.email'       => 'L\'email 2 n\'est pas une adresse valide.',
            'email_3.email'       => 'L\'email 3 n\'est pas une adresse valide.',
            '*.url'               => 'Le lien doit commencer par https://',
            'pays_code.size'      => 'Le code pays doit faire 2 lettres (ex. CI).',
            'logo.image'          => 'Le logo doit être une image.',
            'logo.max'            => 'Le logo ne doit pas dépasser 2 Mo.',
        ]);

        $values = array_intersect_key($data, array_flip(self::TEXT_FIELDS));
        $values = array_merge(array_fill_keys(self::TEXT_FIELDS, null), $values);
        $values['pays_code']        = strtoupper($values['pays_code'] ?? '') ?: null;
        $values['afficher_visites'] = $request->boolean('afficher_visites');

        // Logo : nouveau fichier ou retour au logo par défaut
        $current = Setting::get('logo');
        if ($request->hasFile('logo')) {
            if ($current) Storage::disk('public')->delete($current);
            $values['logo'] = $request->file('logo')->store('settings', 'public');
        } elseif ($request->boolean('logo_reset') && $current) {
            Storage::disk('public')->delete($current);
            $values['logo'] = null;
        }

        Setting::putMany($values);
        Cache::forget(\App\Http\Middleware\CountSiteVisit::CACHE_TOTALS);
        Cache::forget('sitemap_xml');

        Alert::toast('Configuration enregistrée', 'success');
        return redirect()->route('settings.edit');
    }
}
