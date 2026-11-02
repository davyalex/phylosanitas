<?php

namespace App\Console\Commands;

use App\Models\Category;
use Illuminate\Console\Command;

class InitializeCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'categories:initialize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialiser les catégories de base (sondage, actualites)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Initialisation des catégories de base...');

        $categories = [
            [
                'title' => 'sondage',
                'slug' => 'sondage',
                'description' => 'Catégorie pour les sondages',
            ],
            [
                'title' => 'actualites',
                'slug' => 'actualites',
                'description' => 'Catégorie pour les actualités externes',
            ],
        ];

        foreach ($categories as $categoryData) {
            $category = Category::firstOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );

            if ($category->wasRecentlyCreated) {
                $this->info("✓ Catégorie '{$categoryData['title']}' créée avec succès.");
            } else {
                $this->comment("→ Catégorie '{$categoryData['title']}' existe déjà.");
            }
        }

        $this->newLine();
        $this->info('Initialisation terminée !');

        return Command::SUCCESS;
    }
}
