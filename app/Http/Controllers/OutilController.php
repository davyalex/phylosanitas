<?php

namespace App\Http\Controllers;

/**
 * Outils santé (calculs faits dans le navigateur, rien n'est enregistré).
 * La liste des outils est dans config/sante.php.
 */
class OutilController extends Controller
{
    public function index()
    {
        return view('site.pages.outils.index', ['outils' => config('sante.outils')]);
    }

    public function show(string $outil)
    {
        $outils = config('sante.outils');
        abort_unless(isset($outils[$outil]), 404);

        return view('site.pages.outils.' . $outil, [
            'outil'  => $outils[$outil] + ['slug' => $outil],
            'autres' => collect($outils)->except($outil),
        ]);
    }
}
