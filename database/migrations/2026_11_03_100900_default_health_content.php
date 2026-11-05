<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Contenus par défaut des services santé, pour que les pages soient remplies dès la mise en ligne :
 *  - le quiz et les fiches maladies d'exemple passent en ligne ;
 *  - 10 fiches maladies courantes supplémentaires ;
 *  - les grands hôpitaux et centres de référence dans l'annuaire (téléphones à compléter).
 * Idempotent : rien n'est ajouté en double si la migration est rejouée.
 * ⚠ Contenus à relire par l'équipe médicale (voir DOCUMENTATION.md, §14).
 */
return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        // Quiz et fiches d'exemple : en ligne
        DB::table('quizzes')->where('slug', 'idees-recues-sur-la-sante')->update(['actif' => true, 'updated_at' => $now]);
        DB::table('maladies')->whereIn('slug', ['paludisme', 'hypertension-arterielle', 'diabete'])->update(['publie' => true, 'updated_at' => $now]);

        foreach ($this->fiches() as $f) {
            if (DB::table('maladies')->where('slug', $f['slug'])->exists()) continue;
            DB::table('maladies')->insert($f + ['publie' => true, 'created_at' => $now, 'updated_at' => $now]);
        }

        foreach ($this->etablissements() as [$nom, $type, $ville, $quartier, $notes]) {
            if (DB::table('etablissements')->where('nom', $nom)->exists()) continue;
            DB::table('etablissements')->insert([
                'nom' => $nom, 'type' => $type, 'ville' => $ville, 'quartier' => $quartier, 'notes' => $notes,
                'actif' => true, 'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('maladies')->whereIn('slug', array_column($this->fiches(), 'slug'))->delete();
        DB::table('etablissements')->whereIn('nom', array_column($this->etablissements(), 0))->delete();
        DB::table('maladies')->whereIn('slug', ['paludisme', 'hypertension-arterielle', 'diabete'])->update(['publie' => false]);
        DB::table('quizzes')->where('slug', 'idees-recues-sur-la-sante')->update(['actif' => false]);
    }

    private function etablissements(): array
    {
        // [nom, type, ville, quartier, précision]
        return [
            ['CHU de Cocody', 'hopital', 'Abidjan', 'Cocody', 'Centre hospitalier universitaire'],
            ['CHU de Treichville', 'hopital', 'Abidjan', 'Treichville', 'Centre hospitalier universitaire'],
            ['CHU de Yopougon', 'hopital', 'Abidjan', 'Yopougon', 'Centre hospitalier universitaire'],
            ['CHU d\'Angré', 'hopital', 'Abidjan', 'Cocody, Angré', 'Centre hospitalier universitaire'],
            ['Hôpital Mère-Enfant de Bingerville', 'hopital', 'Bingerville', 'Bingerville', 'Maternité et pédiatrie'],
            ['Institut de Cardiologie d\'Abidjan (ICA)', 'hopital', 'Abidjan', 'Treichville', 'Maladies du cœur'],
            ['CHU de Bouaké', 'hopital', 'Bouaké', 'Bouaké', 'Centre hospitalier universitaire'],
            ['Institut Pasteur de Côte d\'Ivoire', 'laboratoire', 'Abidjan', 'Cocody', 'Analyses et recherche biomédicale'],
            ['Centre National de Transfusion Sanguine (CNTS)', 'centre_sante', 'Abidjan', 'Treichville', 'Don de sang'],
            ['Institut National d\'Hygiène Publique (INHP)', 'centre_sante', 'Abidjan', 'Treichville', 'Vaccinations, dont vaccins du voyageur'],
        ];
    }

    private function fiches(): array
    {
        return [
            [
                'nom' => 'Accident vasculaire cérébral (AVC)', 'slug' => 'avc', 'autres_noms' => 'AVC, attaque cérébrale',
                'resume' => 'Un vaisseau du cerveau se bouche ou se rompt : une partie du cerveau manque de sang. C\'est une urgence absolue : chaque minute compte pour limiter les séquelles.',
                'symptomes' => "Bouche déformée, visage qui tombe d'un côté\nFaiblesse ou paralysie d'un bras ou d'une jambe\nDifficulté à parler ou à comprendre\nTrouble brutal de la vue, perte d'équilibre, mal de tête violent et inhabituel",
                'causes' => "Hypertension artérielle (première cause)\nDiabète, tabac, excès de cholestérol\nSurpoids, manque d'activité physique, alcool\nCertaines maladies du cœur",
                'prevention' => "Faire contrôler et traiter sa tension\nNe pas fumer, limiter l'alcool et le sel\nBouger chaque jour, garder un poids raisonnable\nBien suivre ses traitements (tension, diabète, cœur)",
                'traitement' => "À l'hôpital, le plus vite possible : certains traitements ne sont efficaces que dans les premières heures. La rééducation aide ensuite à récupérer.",
                'quand_consulter' => "Urgence : au moindre signe, appelez immédiatement le SAMU, même si les signes disparaissent\nNotez l'heure du début des signes",
                'mots_cles' => 'avc, accident vasculaire, attaque cérébrale, paralysie',
            ],
            [
                'nom' => 'Asthme', 'slug' => 'asthme', 'autres_noms' => 'Crise d\'asthme',
                'resume' => 'Maladie des bronches qui se resserrent par moments, ce qui gêne la respiration. Bien suivi, l\'asthme permet une vie normale.',
                'symptomes' => "Essoufflement, sifflement en respirant\nToux sèche, surtout la nuit ou à l'effort\nSensation d'oppression dans la poitrine",
                'causes' => "Allergies (poussière, acariens, moisissures, pollens, poils d'animaux)\nFumée de tabac, fumée de cuisine au bois ou au charbon, pollution\nInfections respiratoires, effort, air froid",
                'prevention' => "Éviter la fumée et les produits irritants dans la maison\nAérer et dépoussiérer régulièrement\nPrendre le traitement de fond prescrit, même sans crise\nAvoir toujours sur soi son traitement de crise",
                'traitement' => "Un traitement de fond (souvent inhalé) réduit les crises ; un traitement de crise soulage rapidement. Apprenez à bien utiliser votre inhalateur avec un soignant.",
                'quand_consulter' => "Si les crises sont fréquentes ou réveillent la nuit\nUrgence : difficulté à parler, lèvres bleutées, crise qui ne cède pas au traitement",
                'mots_cles' => 'asthme, respiration, bronches',
            ],
            [
                'nom' => 'Choléra', 'slug' => 'cholera', 'autres_noms' => '',
                'resume' => 'Infection de l\'intestin due à une bactérie, transmise par l\'eau ou les aliments souillés. Elle provoque une diarrhée très abondante qui peut déshydrater en quelques heures.',
                'symptomes' => "Diarrhée liquide très abondante (« eau de riz »)\nVomissements\nSoif intense, crampes, grande fatigue\nSignes de déshydratation : bouche sèche, yeux creux, peu d'urines",
                'causes' => "Eau de boisson non potable\nAliments crus ou mal lavés, aliments vendus sans hygiène\nManque d'assainissement et de toilettes",
                'prevention' => "Boire de l'eau potable, bouillie ou traitée\nSe laver les mains au savon avant de manger et après les toilettes\nBien cuire les aliments, laver fruits et légumes à l'eau potable\nUtiliser des toilettes ou latrines",
                'traitement' => "La priorité est de remplacer l'eau perdue : sels de réhydratation orale (SRO) dès le début, et perfusion à l'hôpital si la déshydratation est importante.",
                'quand_consulter' => "Urgence : diarrhée très abondante, surtout chez un enfant ou une personne âgée\nCommencez les SRO en attendant les soins",
                'mots_cles' => 'choléra, cholera, diarrhée',
            ],
            [
                'nom' => 'Dengue', 'slug' => 'dengue', 'autres_noms' => '',
                'resume' => 'Infection virale transmise par la piqûre d\'un moustique (Aedes) qui pique surtout le jour. Elle guérit souvent seule, mais peut parfois devenir grave.',
                'symptomes' => "Fièvre forte et brutale\nMaux de tête, douleur derrière les yeux\nDouleurs dans les muscles et les articulations\nÉruption sur la peau, grande fatigue",
                'causes' => "Piqûre d'un moustique Aedes infecté, surtout en journée\nEaux stagnantes (pneus, pots, récipients) où les moustiques pondent",
                'prevention' => "Vider les récipients d'eau stagnante autour de la maison\nCouvrir les réserves d'eau\nSe protéger des piqûres le jour (vêtements couvrants, répulsifs)",
                'traitement' => "Repos et boire beaucoup. Pour la fièvre, le paracétamol est conseillé ; évitez l'aspirine et l'ibuprofène, qui augmentent le risque de saignement. Un test permet de la distinguer du paludisme.",
                'quand_consulter' => "Toute fièvre doit faire consulter (paludisme possible)\nUrgence : saignements, vomissements répétés, fortes douleurs au ventre, somnolence",
                'mots_cles' => 'dengue, moustique, aedes',
            ],
            [
                'nom' => 'Drépanocytose', 'slug' => 'drepanocytose', 'autres_noms' => 'Anémie falciforme, SS',
                'resume' => 'Maladie héréditaire des globules rouges, qui se déforment et bouchent les petits vaisseaux. Elle est fréquente en Afrique de l\'Ouest. Un suivi régulier change la vie des malades.',
                'symptomes' => "Crises douloureuses (os, ventre, poitrine)\nFatigue, pâleur (anémie), yeux jaunes\nInfections fréquentes\nChez le bébé : mains et pieds gonflés et douloureux",
                'causes' => "Maladie génétique : l'enfant est malade quand il reçoit le gène de ses deux parents\nDeux parents porteurs (AS) ont un risque sur quatre d'avoir un enfant malade (SS) à chaque grossesse",
                'prevention' => "Faire le test (électrophorèse de l'hémoglobine) avant de fonder une famille\nPour les malades : bien boire, éviter le froid et les efforts intenses\nVaccins à jour et traitements préventifs prescrits",
                'traitement' => "Suivi régulier dans un centre spécialisé, antidouleurs pendant les crises, prévention des infections et parfois traitements de fond. Ne pas attendre pour consulter en cas de crise.",
                'quand_consulter' => "Urgence : fièvre chez un enfant drépanocytaire\nUrgence : douleur intense, gêne respiratoire, pâleur brutale, érection douloureuse prolongée, faiblesse d'un côté du corps",
                'mots_cles' => 'drépanocytose, drepanocytose, anémie falciforme',
            ],
            [
                'nom' => 'Fièvre typhoïde', 'slug' => 'fievre-typhoide', 'autres_noms' => 'Typhoïde',
                'resume' => 'Infection due à une bactérie (Salmonella Typhi) transmise par l\'eau ou les aliments souillés. Le diagnostic doit être confirmé par des analyses.',
                'symptomes' => "Fièvre qui monte progressivement et dure\nMaux de tête, grande fatigue\nMaux de ventre, constipation ou diarrhée\nPerte d'appétit",
                'causes' => "Eau ou aliments souillés par des selles\nMains mal lavées\nManque d'assainissement",
                'prevention' => "Boire de l'eau potable\nSe laver les mains au savon\nBien cuire les aliments, laver fruits et légumes à l'eau potable\nUn vaccin existe : demandez conseil",
                'traitement' => "Des antibiotiques prescrits par un médecin, après confirmation par analyses. L'automédication favorise les résistances et peut masquer la maladie.",
                'quand_consulter' => "Une fièvre de plus de 2 à 3 jours doit faire consulter\nUrgence : fortes douleurs au ventre, sang dans les selles, confusion",
                'mots_cles' => 'typhoïde, typhoide',
            ],
            [
                'nom' => 'Hépatite B', 'slug' => 'hepatite-b', 'autres_noms' => 'VHB',
                'resume' => 'Infection du foie par un virus. Elle est souvent silencieuse pendant des années mais peut, sans suivi, abîmer le foie (cirrhose, cancer). Un vaccin efficace existe.',
                'symptomes' => "Souvent aucun symptôme\nParfois : fatigue, yeux et peau jaunes, urines foncées, nausées",
                'causes' => "Transmission de la mère à l'enfant à la naissance\nRapports sexuels non protégés\nContact avec du sang (matériel non stérilisé, rasoirs, objets de toilette partagés)",
                'prevention' => "Vaccination, dès la naissance pour les bébés\nDépistage, notamment pendant la grossesse\nPréservatif, matériel stérile, ne pas partager rasoirs ni brosses à dents",
                'traitement' => "Un suivi régulier du foie est indispensable. Selon les cas, un traitement antiviral au long cours est prescrit.",
                'quand_consulter' => "Pour un dépistage par simple prise de sang\nEn cas de jaunisse (yeux jaunes) ou d'urines foncées",
                'mots_cles' => 'hépatite, hepatite, foie',
            ],
            [
                'nom' => 'Obésité', 'slug' => 'obesite', 'autres_noms' => 'Surpoids',
                'resume' => 'Excès de graisse dans le corps, qui augmente le risque de diabète, d\'hypertension, de maladies du cœur et de certaines douleurs articulaires. Chez l\'adulte, on parle d\'obésité quand l\'IMC atteint 30 ou plus.',
                'symptomes' => "Prise de poids importante, tour de taille élevé\nEssoufflement à l'effort, fatigue\nDouleurs des genoux et du dos\nRonflements, sommeil de mauvaise qualité",
                'causes' => "Alimentation trop riche (sucre, gras, boissons sucrées) et portions importantes\nManque d'activité physique\nFacteurs familiaux, sommeil insuffisant, certains médicaments",
                'prevention' => "Limiter boissons sucrées, fritures et grignotage\nManger plus de légumes, fruits et céréales complètes\nBouger au moins 30 minutes par jour\nSurveiller le poids des enfants",
                'traitement' => "Des changements progressifs et durables de l'alimentation et de l'activité, avec un accompagnement médical. Méfiez-vous des régimes miracles.",
                'quand_consulter' => "Pour être accompagné dans la perte de poids\nPour dépister diabète et hypertension, souvent associés",
                'mots_cles' => 'obésité, obesite, surpoids, imc',
            ],
            [
                'nom' => 'Tuberculose', 'slug' => 'tuberculose', 'autres_noms' => 'TB',
                'resume' => 'Infection due à une bactérie qui touche surtout les poumons et se transmet par l\'air. La tuberculose se soigne et le traitement est gratuit dans les centres de prise en charge.',
                'symptomes' => "Toux qui dure plus de deux semaines, parfois avec du sang\nFièvre et sueurs la nuit\nAmaigrissement, fatigue, perte d'appétit",
                'causes' => "Transmission par l'air : toux et éternuements d'une personne malade non traitée\nRisque plus élevé en cas de VIH, de malnutrition ou de diabète",
                'prevention' => "Vaccin BCG à la naissance\nAérer les pièces, se couvrir la bouche en toussant\nFaire dépister l'entourage d'une personne malade",
                'traitement' => "Plusieurs mois de médicaments, à prendre tous les jours sans interruption, même quand on se sent mieux. Arrêter trop tôt rend la bactérie résistante.",
                'quand_consulter' => "Toute toux de plus de deux semaines doit faire consulter\nEn cas de crachats de sang",
                'mots_cles' => 'tuberculose, toux',
            ],
            [
                'nom' => 'VIH / sida', 'slug' => 'vih-sida', 'autres_noms' => 'VIH, sida, HIV',
                'resume' => 'Le VIH est un virus qui affaiblit les défenses de l\'organisme. Sans traitement, il évolue vers le sida. Avec un traitement pris chaque jour, les personnes vivant avec le VIH ont une vie quasi normale et ne transmettent plus le virus.',
                'symptomes' => "Souvent aucun symptôme pendant des années\nParfois, quelques semaines après la contamination : fièvre, ganglions, éruption, comme une grippe\nPlus tard : infections à répétition, amaigrissement",
                'causes' => "Rapports sexuels non protégés\nContact avec du sang contaminé (aiguilles, objets coupants)\nDe la mère à l'enfant pendant la grossesse, l'accouchement ou l'allaitement, sans traitement",
                'prevention' => "Préservatif\nDépistage régulier, gratuit et confidentiel\nTraitement de la femme enceinte séropositive pour protéger le bébé\nNe jamais partager aiguilles ni objets coupants",
                'traitement' => "Les antirétroviraux, à prendre tous les jours à vie, contrôlent le virus. Commencés tôt, ils protègent la santé et empêchent la transmission.",
                'quand_consulter' => "Pour un dépistage après une prise de risque\nEn urgence (dans les 72 heures) après un rapport à risque ou un accident d'exposition au sang : un traitement préventif existe",
                'mots_cles' => 'vih, sida, hiv, dépistage',
            ],
        ];
    }
};
