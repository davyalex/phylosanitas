<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

/**
 * Bloque les robots sur les formulaires publics (newsletter, commentaires, contact, sondages),
 * sans captcha à cocher pour les visiteurs :
 *  1. champ piège invisible : un humain ne le voit pas, un robot le remplit ;
 *  2. délai minimum entre l'affichage et l'envoi (horodatage chiffré, infalsifiable) ;
 *  3. messages bourrés de liens refusés.
 * Le formulaire doit contenir le composant <x-honeypot />.
 * La limite d'envois par minute est gérée à part (throttle:forms).
 */
class BlockSpamBots
{
    public const FIELD_TRAP = 'site_web';
    public const FIELD_TIME = '_ft';

    private const MIN_SECONDS = 3;          // un robot envoie en moins d'une seconde
    private const MAX_SECONDS = 12 * 3600;  // au-delà, on redemande de recharger la page
    private const MAX_LINKS   = 2;
    private const TEXT_FIELDS = ['message', 'content', 'commentaire', 'name', 'nom', 'subject', 'sujet'];

    public function handle(Request $request, Closure $next)
    {
        if (!$request->isMethod('post')) {
            return $next($request);
        }

        // 1. Champ piège rempli : robot. On fait semblant que tout s'est bien passé.
        if (filled($request->input(self::FIELD_TRAP))) {
            $this->log($request, 'champ piège rempli');
            return redirect()->back();
        }

        // 2. Délai entre l'affichage du formulaire et l'envoi
        $shownAt = 0;
        try {
            $shownAt = (int) Crypt::decryptString((string) $request->input(self::FIELD_TIME));
        } catch (\Throwable) {
            // jeton absent ou falsifié
        }

        if ($shownAt <= 0) {
            $this->log($request, 'jeton absent ou invalide');
            return $this->reject($request, "Le formulaire n'a pas pu être vérifié. Rechargez la page puis réessayez.");
        }

        $age = time() - $shownAt;
        if ($age < self::MIN_SECONDS) {
            $this->log($request, "envoi trop rapide ({$age}s)");
            return $this->reject($request, 'Envoi trop rapide. Patientez quelques secondes puis réessayez.');
        }
        if ($age > self::MAX_SECONDS) {
            return $this->reject($request, 'Le formulaire a expiré. Rechargez la page puis réessayez.');
        }

        // 3. Trop de liens : spam publicitaire
        foreach (self::TEXT_FIELDS as $field) {
            $text = (string) $request->input($field, '');
            if ($text !== '' && preg_match_all('~(https?://|www\.)~i', $text) > self::MAX_LINKS) {
                $this->log($request, 'trop de liens');
                return $this->reject($request, 'Votre message contient trop de liens (' . self::MAX_LINKS . ' maximum).');
            }
        }

        return $next($request);
    }

    private function reject(Request $request, string $message)
    {
        return redirect()->back()
            ->withInput($request->except([self::FIELD_TRAP, self::FIELD_TIME, '_token', 'password']))
            ->with('form_error', $message);
    }

    private function log(Request $request, string $reason): void
    {
        Log::info('Anti-spam : envoi bloqué', [
            'raison' => $reason,
            'url'    => $request->path(),
            'ip'     => $request->ip(),
            'agent'  => substr((string) $request->userAgent(), 0, 160),
        ]);
    }
}
