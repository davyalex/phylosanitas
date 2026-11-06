<?php

/*
|--------------------------------------------------------------------------
| Emplacements publicitaires
|--------------------------------------------------------------------------
| Chaque emplacement affiche une annonce AdSense dès que son identifiant
| (data-ad-slot) est renseigné dans le .env. Sans identifiant, un encart
| « Espace partenaire » discret est affiché si ADS_PLACEHOLDERS=true.
|
|   ADSENSE_CLIENT=ca-pub-XXXXXXXXXXXXXXXX   (identifiant éditeur du compte)
|   ADSENSE_SLOT_LEADERBOARD=1234567890   (bandeau horizontal)
|   ADSENSE_SLOT_RECTANGLE=1234567890     (barre latérale)
|   ADSENSE_SLOT_INFEED=1234567890        (dans les listes d'articles)
|   ADSENSE_SLOT_INARTICLE=1234567890     (dans le corps d'un article)
|   ADS_PLACEHOLDERS=true
*/

return [
    // Identifiant éditeur (ca-pub-…) : seul endroit à changer en cas de nouveau compte.
    // Vide : aucun script AdSense n'est chargé sur le site.
    'client' => env('ADSENSE_CLIENT') ?: null,

    'placeholders' => (bool) env('ADS_PLACEHOLDERS', true),

    'slots' => [
        'leaderboard' => env('ADSENSE_SLOT_LEADERBOARD'),
        'rectangle'   => env('ADSENSE_SLOT_RECTANGLE'),
        'infeed'      => env('ADSENSE_SLOT_INFEED'),
        'inarticle'   => env('ADSENSE_SLOT_INARTICLE'),
    ],

    // Bloc « In-feed » : valeur de data-ad-layout-key fournie par AdSense (ex. -fb+5w+4e-db+86)
    'infeed_layout_key' => env('ADSENSE_INFEED_LAYOUT_KEY'),
];
