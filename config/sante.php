<?php

/*
|--------------------------------------------------------------------------
| Contenus fixes des outils santé
|--------------------------------------------------------------------------
| ⚠ Le calendrier vaccinal ci-dessous est INDICATIF (Programme élargi de
| vaccination de Côte d'Ivoire). Il doit être vérifié par l'équipe médicale
| et mis à jour si le programme national change.
*/

/*
| Modules activables. Masqués = absents du site et du menu d'administration,
| pages publiques en 404 ; le code et les données sont conservés.
| Pour réactiver : MODULE_xxx=true dans .env (puis php artisan config:clear).
*/
$modules = [
    'dossiers'      => (bool) env('MODULE_DOSSIERS', false),
    'notifications' => (bool) env('MODULE_NOTIFICATIONS', false),
    // « Le médecin répond » : masqué en attendant le futur forum
    'questions'     => (bool) env('MODULE_QUESTIONS', false),
    // Pages publiques de l'annuaire et de l'abécédaire des maladies (l'administration de l'annuaire
    // reste active : elle gère les pharmacies de garde)
    'annuaire'      => (bool) env('MODULE_ANNUAIRE', false),
    'maladies'      => (bool) env('MODULE_MALADIES', false),
];

/* Rubriques « Services santé » : menu, pied de page, accueil, liens rapides */
$services = [
    'garde'           => ['label' => 'Pharmacies de garde',     'icon' => 'moon-stars',        'texte' => 'Les pharmacies ouvertes la nuit et le week-end, cette semaine.'],
    'questions.index' => ['label' => 'Le médecin répond',       'icon' => 'chat-heart',        'texte' => 'Posez votre question, un professionnel de santé vous répond.'],
    'quiz.liste'      => ['label' => 'Quiz vrai ou faux',       'icon' => 'patch-question',    'texte' => 'Démêlez le vrai du faux sur les idées reçues en santé.'],
    'outils.index'    => ['label' => 'Outils santé',            'icon' => 'calculator',        'texte' => 'IMC, besoin en eau et calendrier du cycle menstruel.'],
    'maladies.index'  => ['label' => 'Maladies de A à Z',       'icon' => 'journal-medical',   'texte' => 'Symptômes, prévention et quand consulter, en clair.'],
    'dossiers.index'  => ['label' => 'Dossiers',                'icon' => 'collection-play',   'texte' => 'Des parcours de lecture pour tout comprendre sur un sujet.'],
    'annuaire'        => ['label' => 'Annuaire santé',          'icon' => 'geo-alt',           'texte' => 'Pharmacies de garde, hôpitaux et numéros d\'urgence.'],
    'calendrier'      => ['label' => 'Calendrier santé',        'icon' => 'calendar-heart',    'texte' => 'Les grandes journées et campagnes de santé de l\'année.'],
];
if (!$modules['dossiers']) unset($services['dossiers.index']);
if (!$modules['questions']) unset($services['questions.index']);
if (!$modules['annuaire']) unset($services['annuaire']);
if (!$modules['maladies']) unset($services['maladies.index']);

/*
| Outils santé. 'actif' => false : outil masqué (page en 404, absent des listes),
| son code est conservé dans resources/views/site/pages/outils/.
*/
$outils = [
    'imc' => [
        'actif'  => true,
        'titre'  => 'Calculer son IMC',
        'court'  => 'Indice de masse corporelle',
        'texte'  => 'Votre poids est-il adapté à votre taille ? Calculez votre IMC en quelques secondes.',
        'icon'   => 'speedometer2',
        'meta'   => 'Calculez gratuitement votre IMC (indice de masse corporelle) et découvrez ce qu\'il signifie pour votre santé.',
    ],
    'cycle' => [
        'actif'  => true,
        'titre'  => 'Calendrier du cycle menstruel',
        'court'  => 'Cycle menstruel',
        'texte'  => 'Prochaines règles, ovulation et période fertile estimées à partir de vos dernières règles.',
        'icon'   => 'calendar2-heart',
        'meta'   => 'Calculez la date de vos prochaines règles, votre ovulation et votre période fertile à partir de la date de vos dernières règles.',
    ],
    'grossesse' => [
        'actif'  => false,
        'titre'  => 'Date prévue d\'accouchement',
        'court'  => 'Calendrier de grossesse',
        'texte'  => 'À partir de la date des dernières règles : terme prévu, semaines d\'aménorrhée et trimestre en cours.',
        'icon'   => 'calendar-heart',
        'meta'   => 'Calculez la date prévue d\'accouchement, le nombre de semaines d\'aménorrhée et le trimestre de grossesse.',
    ],
    'hydratation' => [
        'actif'  => true,
        'titre'  => 'Combien d\'eau boire par jour ?',
        'court'  => 'Besoin en eau',
        'texte'  => 'Estimez la quantité d\'eau à boire selon votre poids, votre activité et la chaleur.',
        'icon'   => 'droplet-half',
        'meta'   => 'Estimez la quantité d\'eau à boire chaque jour selon votre poids, votre activité physique et la chaleur.',
    ],
    'vaccination' => [
        'actif'  => false,
        'titre'  => 'Calendrier de vaccination de l\'enfant',
        'court'  => 'Vaccins de l\'enfant',
        'texte'  => 'Entrez la date de naissance de votre enfant : voyez les vaccins faits et ceux à venir.',
        'icon'   => 'shield-plus',
        'meta'   => 'Calendrier de vaccination de l\'enfant : entrez sa date de naissance et voyez les dates de chaque vaccin.',
    ],
];

return [

    'modules'  => $modules,

    'services' => $services,

    /*
     * Google Drive des pharmacies de garde (voir App\Services\GoogleDrive) :
     * clé JSON d'un compte de service (chemin relatif à la racine du projet) et ID du dossier.
     * Le lien saisi dans l'administration, s'il y en a un, passe avant GOOGLE_DRIVE_DOSSIER_ID.
     */
    'drive' => [
        'identifiants' => env('GOOGLE_DRIVE_FICHIER_IDENTIFIANTS'),
        'dossier'      => env('GOOGLE_DRIVE_DOSSIER_ID'),
    ],

    'outils'   => array_filter($outils, fn($o) => $o['actif']),

    /* IMC adulte — seuils de l'Organisation mondiale de la santé */
    'imc' => [
        ['max' => 18.5, 'label' => 'Maigreur',            'ton' => 'warn',   'conseil' => 'Votre poids est inférieur à la normale. Parlez-en à un médecin, surtout si vous avez maigri sans raison.'],
        ['max' => 25,   'label' => 'Poids normal',        'ton' => 'ok',     'conseil' => 'Votre poids est adapté à votre taille. Continuez à manger équilibré et à bouger chaque jour.'],
        ['max' => 30,   'label' => 'Surpoids',            'ton' => 'warn',   'conseil' => 'Un léger surpoids augmente le risque d\'hypertension et de diabète. Bouger plus et réduire sucre et graisses aide beaucoup.'],
        ['max' => 35,   'label' => 'Obésité modérée',     'ton' => 'danger', 'conseil' => 'L\'obésité augmente nettement les risques pour le cœur et le diabète. Un médecin peut vous accompagner.'],
        ['max' => 40,   'label' => 'Obésité sévère',      'ton' => 'danger', 'conseil' => 'Un suivi médical est recommandé pour vous aider à perdre du poids sans danger.'],
        ['max' => 999,  'label' => 'Obésité morbide',     'ton' => 'danger', 'conseil' => 'Consultez un médecin : une prise en charge adaptée est importante pour votre santé.'],
    ],

    /*
     * Calendrier vaccinal de l'enfant (PEV Côte d'Ivoire, indicatif).
     * 'jours' = âge en jours à partir de la naissance.
     */
    'vaccins' => [
        ['age' => 'À la naissance', 'jours' => 0,   'vaccins' => ['BCG (tuberculose)', 'Polio oral (VPO 0)']],
        ['age' => '6 semaines',     'jours' => 42,  'vaccins' => ['Pentavalent 1 (diphtérie, tétanos, coqueluche, hépatite B, Haemophilus influenzae b)', 'Polio oral (VPO 1)', 'Pneumocoque 1', 'Rotavirus 1']],
        ['age' => '10 semaines',    'jours' => 70,  'vaccins' => ['Pentavalent 2', 'Polio oral (VPO 2)', 'Pneumocoque 2', 'Rotavirus 2']],
        ['age' => '14 semaines',    'jours' => 98,  'vaccins' => ['Pentavalent 3', 'Polio oral (VPO 3)', 'Pneumocoque 3', 'Polio injectable (VPI)']],
        ['age' => '9 mois',         'jours' => 274, 'vaccins' => ['Rougeole-Rubéole 1 (RR1)', 'Fièvre jaune', 'Méningite A']],
        ['age' => '15 à 18 mois',   'jours' => 457, 'vaccins' => ['Rougeole-Rubéole 2 (RR2)']],
        ['age' => '9 ans (filles)', 'jours' => 3287, 'vaccins' => ['HPV (prévention du cancer du col de l\'utérus)']],
    ],

    'vaccins_note' => 'Le vaccin contre le paludisme est proposé aux jeunes enfants dans certaines zones : renseignez-vous auprès de votre centre de santé.',
];
