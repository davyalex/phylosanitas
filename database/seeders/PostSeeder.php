<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\Category;
use App\Models\OptionSondage;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostSeeder extends Seeder
{
    // Images Picsum par catégorie (seed stable = même image à chaque run)
    private array $images = [
        'sante-public'     => 'https://picsum.photos/seed/sante-publique/800/500',
        'theses-soutenues' => 'https://picsum.photos/seed/medical-thesis/800/500',
        'bon-a-savoir'     => 'https://picsum.photos/seed/health-tips/800/500',
        'scholar'          => 'https://picsum.photos/seed/education-medical/800/500',
        'obesite'          => 'https://picsum.photos/seed/nutrition-health/800/500',
        'actualites'       => 'https://picsum.photos/seed/health-news/800/500',
        'sondage'          => 'https://picsum.photos/seed/survey-medical/800/500',
    ];

    public function run(): void
    {
        $this->command->info('Vidage des tables...');
        $this->truncateTables();

        $this->command->info('Insertion des articles...');
        $this->seedArticles();

        $this->command->info('Insertion des sondages...');
        $this->seedSondages();

        $this->command->info('Insertion des actualites...');
        $this->seedActualites();

        $this->command->info('Seeder termine avec succes.');
    }

    private function truncateTables(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('soumissions')->truncate();
        DB::table('option_sondages')->truncate();
        DB::table('commentaires')->truncate();
        DB::table('media')->where('model_type', 'App\\Models\\Post')->delete();
        DB::table('views')->truncate();
        DB::table('posts')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    private function addImage(Post $post, string $categorySlug): void
    {
        // Essai via URL distante (Picsum), fallback sur image locale
        $url      = $this->images[$categorySlug] ?? 'https://picsum.photos/seed/medical/800/500';
        $fileName = Str::slug($categorySlug) . '-' . $post->id . '.jpg';
        $local    = public_path('assets_site/img/medc.jpg');

        try {
            $post->addMediaFromUrl($url)
                 ->usingFileName($fileName)
                 ->toMediaCollection('image');
        } catch (\Throwable) {
            // Fallback : image locale
            try {
                $post->addMediaFromDisk($local, 'local')
                     ->usingFileName($fileName)
                     ->toMediaCollection('image');
            } catch (\Throwable) {
                // Dernier recours : copie physique vers media
                try {
                    $post->addMediaFromPath($local)
                         ->usingFileName($fileName)
                         ->toMediaCollection('image');
                } catch (\Throwable $e) {
                    $this->command->warn('   Image ignoree pour post #' . $post->id . ' : ' . $e->getMessage());
                }
            }
        }
    }

    private function seedArticles(): void
    {
        $articles = [
            // Sante publique
            [
                'cat_slug'    => 'sante-public',
                'title'       => 'La vaccination contre la grippe : ce que vous devez savoir',
                'description' => '<p>La grippe saisonniere touche chaque annee entre 2 et 8 millions de personnes en Cote d\'Ivoire. La vaccination reste le moyen le plus efficace pour se proteger et proteger son entourage.</p><h3>Qui doit se faire vacciner ?</h3><ul><li>Les personnes agees de 65 ans et plus</li><li>Les femmes enceintes a partir du 2eme trimestre</li><li>Les personnes souffrant de maladies chroniques (diabete, asthme, maladies cardiovasculaires)</li><li>Les professionnels de sante</li></ul><h3>Comment agit le vaccin ?</h3><p>Le vaccin contre la grippe stimule le systeme immunitaire pour produire des anticorps specifiques. Son efficacite varie de 40 a 70 % selon les annees.</p><p><strong>N\'hesitez pas a consulter votre medecin pour plus d\'informations.</strong></p>',
            ],
            [
                'cat_slug'    => 'sante-public',
                'title'       => 'Paludisme en Cote d\'Ivoire : situation epidemiologique 2025',
                'description' => '<p>Le paludisme demeure la premiere cause de consultation dans les formations sanitaires de Cote d\'Ivoire. Le pays enregistre chaque annee plus de 6 millions de cas confirmes.</p><h3>Les zones a risque</h3><p>Toutes les regions du pays sont touchees, avec une prevalence plus elevee dans les zones rurales et forestieres du centre-ouest et du sud.</p><h3>Mesures de prevention recommandees</h3><ul><li><strong>Les moustiquaires impregnees d\'insecticide</strong> : leur utilisation systematique reduit la transmission de 50 %</li><li><strong>La chimioprevention</strong> : particulierement recommandee pour les femmes enceintes et les enfants de moins de 5 ans</li></ul><p>En cas de fievre, consultez un professionnel de sante immediatement.</p>',
            ],
            [
                'cat_slug'    => 'sante-public',
                'title'       => 'Hypertension arterielle : l\'epidemie silencieuse',
                'description' => '<p>En Afrique subsaharienne, <strong>46 % des adultes souffrent d\'hypertension arterielle</strong>, dont une grande majorite sans le savoir. Cette "maladie silencieuse" est le premier facteur de risque d\'AVC et d\'infarctus.</p><h3>Les facteurs de risque modifiables</h3><ul><li>Consommation excessive de sel (plus de 5g par jour)</li><li>Surpoids et obesite</li><li>Sedentarite</li><li>Tabagisme et consommation d\'alcool</li><li>Stress chronique</li></ul><blockquote><p><em>Un simple brassard autour du bras peut vous sauver la vie. Mesurez votre tension regulierement.</em></p></blockquote>',
            ],

            // Bon a savoir
            [
                'cat_slug'    => 'bon-a-savoir',
                'title'       => '10 aliments qui renforcent votre systeme immunitaire',
                'description' => '<p>Votre alimentation joue un role fondamental dans le fonctionnement de votre systeme immunitaire. Voici les 10 aliments a privilegier pour booster vos defenses naturelles.</p><h3>1. Les agrumes</h3><p>Riches en vitamine C, les oranges, citrons, pamplemousses et mandarines stimulent la production de globules blancs.</p><h3>2. L\'ail</h3><p>L\'ail contient de l\'allicine, un compose aux puissantes proprietes antimicrobiennes. Des etudes ont montre qu\'une consommation reguliere reduit la frequence des rhumes de 63 %.</p><h3>3. Le gingembre</h3><p>Reconnu pour ses proprietes anti-inflammatoires, le gingembre aide a reduire les douleurs et a combattre les nausees.</p><h3>4. Le curcuma</h3><p>La curcumine, principe actif du curcuma, possede d\'importantes proprietes anti-inflammatoires. Sa biodisponibilite est multipliee par 20 en l\'associant au poivre noir.</p>',
            ],
            [
                'cat_slug'    => 'bon-a-savoir',
                'title'       => 'Sommeil et sante : pourquoi 7 heures ne suffisent pas pour tout le monde',
                'description' => '<p>Le mythe des "8 heures de sommeil" pour tout le monde est depasse. La recherche scientifique montre que les besoins en sommeil varient considerablement d\'une personne a l\'autre.</p><h3>Les cycles du sommeil expliques</h3><p>Un cycle de sommeil dure environ 90 minutes et se compose de plusieurs phases :</p><ul><li><strong>Le sommeil leger</strong> (N1 et N2) : transition vers le sommeil profond</li><li><strong>Le sommeil profond</strong> (N3) : phase de recuperation physique</li><li><strong>Le sommeil paradoxal (REM)</strong> : traitement emotionnel, creativite</li></ul><h3>Les signes d\'un manque de sommeil</h3><ul><li>Irritabilite et sautes d\'humeur</li><li>Difficultes de concentration et de memorisation</li><li>Affaiblissement du systeme immunitaire</li></ul>',
            ],
            [
                'cat_slug'    => 'bon-a-savoir',
                'title'       => 'Activite physique : comment demarrer quand on n\'a pas l\'habitude',
                'description' => '<p>L\'Organisation Mondiale de la Sante recommande au moins <strong>150 minutes d\'activite physique moderee par semaine</strong> pour les adultes. Voici un guide pratique pour commencer en douceur.</p><h3>La regle des 5 minutes</h3><p>L\'obstacle principal n\'est pas la fatigue, c\'est le demarrage. La regle des 5 minutes est simple : engagez-vous a faire seulement 5 minutes d\'exercice. La plupart du temps, une fois lance, vous continuerez naturellement.</p><h3>Choisir une activite adaptee</h3><ul><li><strong>La marche rapide</strong> : accessible a tous, zero equipement</li><li><strong>La natation</strong> : ideale pour les articulations sensibles</li><li><strong>Le velo</strong> : peut se pratiquer dans la vie quotidienne</li><li><strong>Le yoga</strong> : parfait pour commencer a se reconnecter a son corps</li></ul>',
            ],

            // Theses
            [
                'cat_slug'    => 'theses-soutenues',
                'title'       => 'Prevalence des infections urinaires chez la femme enceinte a Abidjan',
                'description' => '<h3>Resume de these</h3><p><strong>Auteur :</strong> Dr. Adjoua Kouame Bintou<br><strong>Soutenue le :</strong> 15 mars 2025<br><strong>Specialite :</strong> Gynecologie-Obstetrique</p><h3>Resultats principaux</h3><ul><li>Prevalence de 23,4 % d\'infections urinaires confirmees</li><li><em>Escherichia coli</em> represente 58 % des germes isoles</li><li>Resistance a l\'amoxicilline : 72 % des souches</li><li>Sensibilite conservee a la nitrofurantoine : 94 %</li></ul><h3>Conclusion</h3><p>La prevalence elevee des infections urinaires souligne l\'urgence d\'un depistage systematique et d\'une antibiotherapie guidee par l\'antibiogramme.</p>',
            ],
            [
                'cat_slug'    => 'theses-soutenues',
                'title'       => 'Impact du diabete de type 2 sur la qualite de vie des patients au CHU de Cocody',
                'description' => '<h3>Resume de these</h3><p><strong>Auteur :</strong> Dr. Konan Jean-Marc Ahoussou<br><strong>Soutenue le :</strong> 22 novembre 2024<br><strong>Specialite :</strong> Medecine Interne</p><h3>Resultats</h3><ul><li>Score global de qualite de vie moyen : 54,2/100 (vs 73,8 en population generale)</li><li>Composantes les plus alterees : vitalite (41,3) et sante mentale (48,7)</li><li>Correlation negative significative entre anciennete du diabete et qualite de vie</li></ul><h3>Conclusion</h3><p>Le diabete de type 2 altere significativement la qualite de vie. Une prise en charge multidisciplinaire est indispensable.</p>',
            ],

            // Scolaire
            [
                'cat_slug'    => 'scholar',
                'title'       => 'Comment reussir ses etudes de medecine : conseils d\'un etudiant en 6eme annee',
                'description' => '<p>Les etudes de medecine sont souvent decrites comme les plus exigeantes qui soient. Voici les strategies qui m\'ont permis de traverser ce cursus avec succes.</p><h3>1. La regularite avant tout</h3><p>En medecine, il est impossible de "tout reviser a la derniere minute". <strong>Travailler 3h par jour regulierement vaut mieux que 12h la veille de l\'examen.</strong></p><h3>2. Les fiches de synthese</h3><p>Apres chaque cours, creez une fiche d\'une page maximum. Le cerveau retient mieux l\'information structuree visuellement.</p><h3>3. Prendre soin de soi</h3><p>Paradoxalement, les etudiants en medecine sont souvent les plus mauvais patients. Dormez suffisamment, mangez equilibre, pratiquez une activite physique.</p>',
            ],
            [
                'cat_slug'    => 'scholar',
                'title'       => 'Les meilleures ressources en ligne pour les etudiants en sciences medicales',
                'description' => '<p>A l\'ere du numerique, l\'acces aux ressources medicales de qualite n\'a jamais ete aussi democratise. Voici une selection des meilleures plateformes.</p><h3>Ressources gratuites</h3><ul><li><strong>PubMed</strong> : la base de donnees de reference de la litterature medicale internationale.</li><li><strong>WHO IRIS</strong> : bibliotheque numerique de l\'OMS, utile pour les donnees epidemiologiques africaines.</li><li><strong>Khan Academy Medicine</strong> : cours structures en biologie et medecine de base.</li></ul><h3>Applications mobiles recommandees</h3><ul><li><strong>Anki</strong> : le meilleur systeme de repetition espacee pour memoriser les medicaments.</li><li><strong>Medscape</strong> : references medicales, calculateurs de doses, interactions medicamenteuses.</li></ul>',
            ],

            // Obesite
            [
                'cat_slug'    => 'obesite',
                'title'       => 'Obesite en Afrique : une transition nutritionnelle a double tranchant',
                'description' => '<p>Longtemps associee aux pays a revenus eleves, l\'obesite est aujourd\'hui en forte progression en Afrique subsaharienne. En Cote d\'Ivoire, la prevalence du surpoids et de l\'obesite chez l\'adulte a double en 20 ans.</p><h3>La transition nutritionnelle en cause</h3><p>Cette augmentation s\'explique par une <strong>transition nutritionnelle rapide</strong> : abandon des regimes alimentaires traditionnels au profit d\'une alimentation ultra-transformee.</p><h3>Les consequences sur la sante</h3><ul><li>Diabete de type 2 (risque x 7)</li><li>Hypertension arterielle (risque x 3,5)</li><li>Maladies cardiovasculaires (risque x 2)</li><li>Certains cancers (colon, sein, endometre)</li></ul>',
            ],
            [
                'cat_slug'    => 'obesite',
                'title'       => 'Comprendre l\'IMC : utilite et limites de cet indicateur',
                'description' => '<p>L\'Indice de Masse Corporelle (IMC) est l\'indicateur le plus utilise pour evaluer le poids corporel. Simple a calculer (poids en kg divise par la taille en m2), il est pourtant souvent mal interprete.</p><h3>Les seuils de l\'OMS</h3><table><thead><tr><th>IMC</th><th>Classification</th></tr></thead><tbody><tr><td>Moins de 18,5</td><td>Maigreur</td></tr><tr><td>18,5 - 24,9</td><td>Corpulence normale</td></tr><tr><td>25 - 29,9</td><td>Surpoids</td></tr><tr><td>30 et plus</td><td>Obesite</td></tr></tbody></table><h3>Les limites de l\'IMC</h3><p>L\'IMC <strong>ne distingue pas la masse musculaire de la masse grasse</strong>. Un sportif muscle peut avoir un IMC de "surpoids" alors que sa composition corporelle est excellente.</p>',
            ],
        ];

        foreach ($articles as $data) {
            $category = Category::whereSlug($data['cat_slug'])->first();
            if (!$category) {
                $this->command->warn('   Categorie introuvable : ' . $data['cat_slug']);
                continue;
            }

            $post = Post::create([
                'title'       => $data['title'],
                'description' => $data['description'],
                'category_id' => $category->id,
                'published'   => 'public',
                'user_id'     => 1,
            ]);

            $this->addImage($post, $data['cat_slug']);
            $this->command->line('   [OK] ' . Str::limit($data['title'], 60));
        }
    }

    private function seedSondages(): void
    {
        $category = Category::whereSlug('sondage')->first();
        if (!$category) {
            $this->command->warn('   Categorie sondage introuvable.');
            return;
        }

        $sondages = [
            [
                'description' => '<p>A quelle frequence consultez-vous un medecin, meme en dehors de toute maladie ?</p>',
                'options'     => [
                    'Une fois par an (bilan annuel)',
                    'Tous les 6 mois',
                    'Seulement quand je suis malade',
                    'Jamais, je ne vais pas chez le medecin',
                ],
            ],
            [
                'description' => '<p>Quel est selon vous le principal obstacle a l\'acces aux soins de sante en Cote d\'Ivoire ?</p>',
                'options'     => [
                    'Le cout des soins et medicaments',
                    'L\'eloignement des structures de sante',
                    'Le manque de personnel medical qualifie',
                    'La mefiance envers la medecine moderne',
                ],
            ],
            [
                'description' => '<p>Pratiquez-vous une activite physique reguliere (au moins 30 min, 3x par semaine) ?</p>',
                'options'     => [
                    'Oui, regulierement',
                    'Oui, mais pas assez regulierement',
                    'Rarement',
                    'Non, jamais',
                ],
            ],
        ];

        foreach ($sondages as $data) {
            $post = Post::create([
                'slug'        => 'sondage-' . Str::random(6),
                'description' => $data['description'],
                'category_id' => $category->id,
                'published'   => 'public',
            ]);

            foreach ($data['options'] as $title) {
                OptionSondage::create([
                    'post_id' => $post->id,
                    'title'   => $title,
                ]);
            }

            $this->addImage($post, 'sondage');
            $this->command->line('   [OK] Sondage : ' . Str::limit(strip_tags($data['description']), 50));
        }
    }

    private function seedActualites(): void
    {
        $category = Category::whereSlug('actualites')->first();
        if (!$category) {
            $this->command->warn('   Categorie actualites introuvable.');
            return;
        }

        $actualites = [
            [
                'title'         => 'Campagne nationale de vaccination contre la poliomyelite — Mai 2025',
                'description'   => '<p>Le Ministere de la Sante lance une campagne nationale de vaccination contre la poliomyelite du 12 au 16 mai 2025. Cette campagne cible les enfants de 0 a 5 ans sur l\'ensemble du territoire national. <strong>La vaccination est gratuite et accessible dans tous les centres de sante.</strong></p>',
                'actualite_une' => 1,
            ],
            [
                'title'         => 'Journee mondiale de la sante mentale — 10 Octobre 2025',
                'description'   => '<p>PhyloSanitas s\'associe a la Journee Mondiale de la Sante Mentale pour sensibiliser sur les troubles anxieux et depressifs. Des consultations gratuites seront disponibles dans plusieurs villes du pays.</p>',
                'actualite_une' => 1,
            ],
            [
                'title'         => 'Nouveau traitement contre le diabete approuve en Afrique de l\'Ouest',
                'description'   => '<p>L\'autorite de reglementation pharmaceutique de la CEDEAO vient d\'approuver un nouveau traitement contre le diabete de type 2. Ce medicament sera disponible dans les pharmacies agreees a partir de juillet 2025.</p>',
                'actualite_une' => 0,
            ],
            [
                'title'         => 'Ouverture d\'un nouveau centre de dialyse a Abidjan',
                'description'   => '<p>Un nouveau centre de dialyse de 40 postes a ouvert ses portes a Cocody. Cette structure, financee en partenariat public-prive, renforce l\'offre de soins pour les patients atteints d\'insuffisance renale chronique.</p>',
                'actualite_une' => 0,
            ],
        ];

        foreach ($actualites as $data) {
            $post = Post::create([
                'title'         => $data['title'],
                'description'   => $data['description'],
                'category_id'   => $category->id,
                'published'     => 'public',
                'actualite_une' => $data['actualite_une'],
                'user_id'       => 1,
            ]);

            $this->addImage($post, 'actualites');
            $this->command->line('   [OK] ' . Str::limit($data['title'], 60));
        }
    }
}
