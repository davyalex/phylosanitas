<?php

namespace App\Http\Controllers;

use App\Models\AvisArticle;
use App\Models\Post;
use App\Models\PushAbonne;
use App\Models\PushMessage;
use App\Models\Setting;
use App\Services\WebPush;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

/**
 * Fidélisation des lecteurs :
 *  - « Cet article vous a-t-il aidé ? »
 *  - notifications (abonnement des appareils + envoi depuis l'administration)
 */
class EngagementController extends Controller
{
    /* ---------- Avis sur un article ---------- */

    public function avis(Request $request, Post $post)
    {
        $data = $request->validate(['utile' => 'required|boolean']);
        abort_unless($post->published === 'public', 404);

        AvisArticle::updateOrCreate(
            ['post_id' => $post->id, 'visiteur' => hash('sha256', $request->session()->getId() . '|' . $post->id)],
            ['utile' => (bool) $data['utile']]
        );

        $request->session()->put("avis.{$post->id}", (bool) $data['utile']);

        return response()->json(['ok' => true]);
    }

    /* ---------- Notifications : côté visiteur ---------- */

    public function cle()
    {
        abort_unless(config('sante.modules.notifications'), 404);
        $cle = WebPush::clePublique();

        return response()->json(['cle' => $cle], $cle ? 200 : 503);
    }

    public function abonner(Request $request)
    {
        abort_unless(config('sante.modules.notifications'), 404);
        $data = $request->validate([
            'endpoint'    => ['required', 'url', 'max:2000', 'regex:#^https://#'],
            'keys.p256dh' => 'nullable|string|max:255',
            'keys.auth'   => 'nullable|string|max:255',
        ]);

        PushAbonne::updateOrCreate(
            ['endpoint_hash' => hash('sha256', $data['endpoint'])],
            [
                'endpoint'   => $data['endpoint'],
                'p256dh'     => $data['keys']['p256dh'] ?? null,
                'auth'       => $data['keys']['auth'] ?? null,
                'navigateur' => substr((string) $request->userAgent(), 0, 255),
                'echecs'     => 0,
            ]
        );

        return response()->json(['ok' => true]);
    }

    public function desabonner(Request $request)
    {
        $endpoint = (string) $request->input('endpoint');
        if ($endpoint) PushAbonne::where('endpoint_hash', hash('sha256', $endpoint))->delete();

        return response()->json(['ok' => true]);
    }

    /** Lue par le service worker à la réception d'une notification */
    public function derniere()
    {
        $m = PushMessage::latest('id')->first();
        $site = Setting::forViews();

        return response()->json($m ? [
            'titre' => $m->titre,
            'corps' => $m->corps,
            'url'   => $m->url,
            'tag'   => 'ps-' . $m->id,
        ] : [
            'titre' => $site['nom'],
            'corps' => 'De nouveaux conseils santé vous attendent.',
            'url'   => '/',
            'tag'   => 'ps-0',
        ])->header('Cache-Control', 'no-store');
    }

    /* ---------- Notifications : administration ---------- */

    public function admin()
    {
        abort_unless(config('sante.modules.notifications'), 404);
        $abonnes  = PushAbonne::count();
        $messages = PushMessage::latest()->take(30)->get();
        $actif    = WebPush::actif();
        $auto     = (bool) (int) Setting::get('push_auto', 1);
        $derniers = Post::where('published', 'public')->whereNotNull('title')->latest()->take(15)->get(['id', 'title', 'slug']);

        return view('admin.pages.push.index', compact('abonnes', 'messages', 'actif', 'auto', 'derniers'));
    }

    public function envoyer(Request $request)
    {
        abort_unless(config('sante.modules.notifications'), 404);
        $data = $request->validate([
            'titre' => 'required|string|max:110',
            'corps' => 'nullable|string|max:200',
            'url'   => ['required', 'string', 'max:500', 'regex:#^(https?://|/)#i'],
        ], [
            'titre.required' => 'Indiquez le titre de la notification.',
            'url.required'   => 'Indiquez la page à ouvrir au clic.',
            'url.regex'      => 'Le lien doit commencer par / (page du site) ou https://',
        ]);

        // Lien vers le site : on garde le chemin seulement
        if (str_starts_with($data['url'], url('/'))) {
            $data['url'] = '/' . ltrim(substr($data['url'], strlen(url('/'))), '/');
        }

        $message = PushMessage::create($data);
        [$ok, $ko] = WebPush::envoyer($message);

        Alert::toast("Notification envoyée à {$ok} appareil(s)" . ($ko ? " · {$ko} échec(s)" : ''), $ok ? 'success' : 'warning');
        return back();
    }

    public function reglages(Request $request)
    {
        abort_unless(config('sante.modules.notifications'), 404);
        Setting::putMany(['push_auto' => $request->boolean('push_auto')]);

        Alert::toast('Réglage enregistré', 'success');
        return back();
    }
}
