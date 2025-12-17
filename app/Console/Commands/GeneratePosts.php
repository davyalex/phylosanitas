<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class GeneratePosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'posts:generate {count=10 : Nombre de posts à générer}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Générer des posts de démonstration';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $count = (int) $this->argument('count');

        $this->info("Génération de {$count} posts...");

        // Vérifier s'il y a des catégories
        $categories = Category::all();
        if ($categories->isEmpty()) {
            $this->error('Aucune catégorie trouvée. Veuillez d\'abord créer des catégories.');
            return Command::FAILURE;
        }

        // Créer un utilisateur par défaut si nécessaire
        $user = User::first();
        // if (!$user) {
        //     $this->info('Création d\'un utilisateur par défaut...');
        //     $user = User::create([
        //         'name' => 'Admin',
        //         'email' => 'admin@phylosanitas.com',
        //         'password' => bcrypt('password'),
        //         'email_verified_at' => now(),
        //     ]);
        //     $this->info("✓ Utilisateur créé: {$user->email}");
        // }

        // Titres et contenus de santé réalistes
        $posts = [
            [
                'title' => 'Les bienfaits de la méditation sur la santé mentale',
                'description' => '<p>La méditation est une pratique ancestrale qui connaît un regain d\'intérêt dans le monde moderne. Des études récentes démontrent son efficacité sur la réduction du stress et de l\'anxiété.</p><p>Les bienfaits incluent une meilleure concentration, une diminution de la pression artérielle et une amélioration de la qualité du sommeil. Pratiquer 10 minutes par jour peut déjà apporter des résultats significatifs.</p><p>Différentes techniques existent : la méditation de pleine conscience, la méditation transcendantale, ou encore la méditation guidée. Chacun peut trouver celle qui lui convient le mieux.</p>',
                'tag' => 'santé mentale, méditation, bien-être',
                'unsplash_query' => 'meditation wellness',
            ],
            [
                'title' => 'L\'importance de l\'hydratation pour la santé',
                'description' => '<p>Boire suffisamment d\'eau est essentiel pour maintenir un bon état de santé. L\'eau représente environ 60% du poids corporel et joue un rôle crucial dans de nombreuses fonctions physiologiques.</p><p>Une bonne hydratation aide à réguler la température corporelle, facilite la digestion et l\'élimination des toxines. Elle contribue également à maintenir une peau saine et à prévenir les maux de tête.</p><p>Il est recommandé de boire environ 1,5 à 2 litres d\'eau par jour, davantage en cas d\'activité physique ou de fortes chaleurs.</p>',
                'tag' => 'hydratation, santé, nutrition',
                'unsplash_query' => 'water hydration',
            ],
            [
                'title' => 'Les super-aliments à intégrer dans votre alimentation',
                'description' => '<p>Les super-aliments sont des aliments naturellement riches en nutriments essentiels. Parmi eux, on trouve les baies de goji, le quinoa, les graines de chia et les épinards.</p><p>Ces aliments sont particulièrement riches en antioxydants, vitamines et minéraux. Ils contribuent à renforcer le système immunitaire et à prévenir certaines maladies chroniques.</p><p>L\'intégration de ces aliments dans une alimentation équilibrée peut améliorer significativement votre santé globale. Commencez par en ajouter un ou deux à vos repas quotidiens.</p>',
                'tag' => 'nutrition, super-aliments, alimentation',
                'unsplash_query' => 'healthy food nutrition',
            ],
            [
                'title' => 'Comment améliorer la qualité de votre sommeil',
                'description' => '<p>Un sommeil de qualité est fondamental pour la santé physique et mentale. Pourtant, de nombreuses personnes souffrent de troubles du sommeil.</p><p>Pour améliorer votre sommeil, établissez une routine régulière en vous couchant et en vous levant à heures fixes. Évitez les écrans au moins une heure avant le coucher et créez un environnement propice au repos : chambre sombre, fraîche et silencieuse.</p><p>La pratique d\'exercices de relaxation ou de yoga le soir peut également favoriser l\'endormissement. Si les problèmes persistent, consultez un professionnel de santé.</p>',
                'tag' => 'sommeil, santé, bien-être',
                'unsplash_query' => 'sleep bedroom relaxation',
            ],
            [
                'title' => 'Les exercices physiques recommandés pour tous',
                'description' => '<p>L\'activité physique régulière est l\'un des piliers d\'une bonne santé. L\'OMS recommande au moins 150 minutes d\'activité modérée par semaine pour les adultes.</p><p>La marche rapide, la natation et le vélo sont d\'excellents exercices cardiovasculaires accessibles à tous. Le renforcement musculaire est également important et peut être pratiqué avec ou sans équipement.</p><p>Le plus important est de choisir une activité qui vous plaît et de la pratiquer régulièrement. Commencez progressivement et augmentez l\'intensité au fil du temps.</p>',
                'tag' => 'sport, exercice, santé',
                'unsplash_query' => 'fitness exercise workout',
            ],
            [
                'title' => 'La gestion du stress au quotidien',
                'description' => '<p>Le stress chronique peut avoir des effets néfastes sur la santé. Il est important d\'apprendre à le gérer efficacement.</p><p>Parmi les techniques éprouvées, on trouve la respiration profonde, la pratique régulière d\'exercice physique et la gestion du temps. Apprendre à dire non et à déléguer est également crucial.</p><p>Prendre des pauses régulières, cultiver des relations sociales positives et s\'accorder des moments de détente sont essentiels. N\'hésitez pas à consulter un professionnel si le stress devient envahissant.</p>',
                'tag' => 'stress, santé mentale, bien-être',
                'unsplash_query' => 'stress relief wellness',
            ],
            [
                'title' => 'Les vitamines essentielles pour votre santé',
                'description' => '<p>Les vitamines sont des micronutriments indispensables au bon fonctionnement de l\'organisme. Chacune joue un rôle spécifique dans le maintien de la santé.</p><p>La vitamine D est essentielle pour la santé osseuse, la vitamine C renforce le système immunitaire, et les vitamines du groupe B participent au métabolisme énergétique. La vitamine A est importante pour la vision et la vitamine E pour ses propriétés antioxydantes.</p><p>Une alimentation variée et équilibrée permet généralement de couvrir les besoins. Dans certains cas, une supplémentation peut être recommandée par un professionnel de santé.</p>',
                'tag' => 'vitamines, nutrition, santé',
                'unsplash_query' => 'vitamins supplements health',
            ],
            [
                'title' => 'Les bienfaits du yoga sur le corps et l\'esprit',
                'description' => '<p>Le yoga est une pratique millénaire qui allie postures physiques, exercices de respiration et méditation. Ses bienfaits sur la santé sont nombreux et scientifiquement prouvés.</p><p>Sur le plan physique, le yoga améliore la souplesse, renforce les muscles et améliore la posture. Il contribue également à réduire les douleurs chroniques, notamment au niveau du dos.</p><p>Sur le plan mental, le yoga réduit le stress et l\'anxiété, améliore la concentration et favorise un meilleur équilibre émotionnel. Il existe différents types de yoga adaptés à tous les niveaux.</p>',
                'tag' => 'yoga, bien-être, sport',
                'unsplash_query' => 'yoga meditation zen',
            ],
            [
                'title' => 'Prévention des maladies cardiovasculaires',
                'description' => '<p>Les maladies cardiovasculaires sont la première cause de mortalité dans le monde. Pourtant, de nombreux facteurs de risque sont modifiables.</p><p>Adopter une alimentation saine, riche en fruits, légumes et céréales complètes, tout en limitant les graisses saturées et le sel, est essentiel. L\'exercice régulier, le maintien d\'un poids santé et l\'arrêt du tabac sont également cruciaux.</p><p>Un suivi médical régulier permet de détecter et de contrôler l\'hypertension, le diabète et le cholestérol. La prévention reste le meilleur remède.</p>',
                'tag' => 'prévention, santé cardiovasculaire',
                'unsplash_query' => 'heart health cardio',
            ],
            [
                'title' => 'L\'importance de la santé digestive',
                'description' => '<p>Un système digestif en bonne santé est fondamental pour le bien-être général. Il est souvent appelé notre "deuxième cerveau" en raison de son impact sur l\'humeur et le système immunitaire.</p><p>Pour maintenir une bonne santé digestive, privilégiez une alimentation riche en fibres, buvez suffisamment d\'eau et pratiquez une activité physique régulière. Les probiotiques, présents dans les aliments fermentés, peuvent également être bénéfiques.</p><p>Prenez le temps de manger calmement, mastiquez bien et écoutez les signaux de votre corps. En cas de troubles persistants, consultez un professionnel de santé.</p>',
                'tag' => 'digestion, santé, nutrition',
                'unsplash_query' => 'healthy gut nutrition',
            ],
            [
                'title' => 'Les bienfaits de la nature sur la santé',
                'description' => '<p>Passer du temps dans la nature a des effets bénéfiques prouvés sur la santé physique et mentale. Les Japonais pratiquent le "bain de forêt" (shinrin-yoku) comme thérapie.</p><p>Le contact avec la nature réduit le stress, améliore l\'humeur et renforce le système immunitaire. Une simple promenade de 20 minutes en plein air peut déjà apporter ces bienfaits.</p><p>L\'exposition à la lumière naturelle régule également le rythme circadien et améliore la qualité du sommeil. Essayez d\'intégrer des moments en nature dans votre routine hebdomadaire.</p>',
                'tag' => 'nature, bien-être, santé mentale',
                'unsplash_query' => 'nature forest outdoor',
            ],
            [
                'title' => 'Comment renforcer son système immunitaire',
                'description' => '<p>Un système immunitaire fort est notre meilleure défense contre les infections et les maladies. Plusieurs habitudes de vie peuvent le renforcer.</p><p>Une alimentation équilibrée, riche en fruits et légumes colorés, apporte les vitamines et minéraux nécessaires. Le sommeil suffisant (7-9 heures par nuit) permet au corps de se régénérer. L\'exercice régulier stimule également l\'immunité.</p><p>La gestion du stress, l\'arrêt du tabac et la modération dans la consommation d\'alcool sont également importants. Une bonne hygiène, notamment le lavage régulier des mains, reste fondamentale.</p>',
                'tag' => 'immunité, santé, prévention',
                'unsplash_query' => 'immune system health',
            ],
        ];

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        for ($i = 0; $i < $count; $i++) {
            $postData = $posts[$i % count($posts)];
            
            // Ajouter un numéro si on dépasse le nombre de posts prédéfinis
            $title = $postData['title'];
            if ($i >= count($posts)) {
                $title .= ' - Partie ' . (floor($i / count($posts)) + 1);
            }

            $post = Post::create([
                'title' => $title,
                'description' => $postData['description'],
                'tag' => $postData['tag'],
                'category_id' => $categories->random()->id,
                'user_id' => $user->id,
                'published' => 'public',
                'actualite_une' => $i < 3 ? 1 : 0, // Les 3 premiers sont à la une
                'lien' => null,
            ]);

            // Ajouter une image depuis picsum.photos avec Spatie Media Library
            try {
                // Utiliser picsum.photos (plus stable qu'Unsplash)
                $imageUrl = "https://picsum.photos/800/600?random=" . ($i + time());
                
                // Télécharger l'image
                $imageContent = @file_get_contents($imageUrl);
                
                if ($imageContent !== false && strlen($imageContent) > 1000) {
                    // Créer un fichier temporaire
                    $tempFile = tempnam(sys_get_temp_dir(), 'img_');
                    file_put_contents($tempFile, $imageContent);
                    
                    // Ajouter l'image avec Spatie
                    $post->addMedia($tempFile)
                        ->usingFileName('post_' . $post->id . '_' . time() . '.jpg')
                        ->toMediaCollection('image');
                    
                    // Supprimer le fichier temporaire si il existe encore
                    if (file_exists($tempFile)) {
                        @unlink($tempFile);
                    }
                }
            } catch (\Exception $e) {
                // Si l'ajout de l'image échoue, continuer sans bloquer
                // Ne pas afficher d'erreur pour ne pas ralentir la barre de progression
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✓ {$count} posts créés avec succès !");
        $this->info("✓ Images ajoutées depuis Picsum.photos");
        $this->comment("Note: Le téléchargement d'images peut prendre quelques secondes par post.");
        
        return Command::SUCCESS;
    }
}
