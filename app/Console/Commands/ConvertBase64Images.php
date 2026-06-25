<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ConvertBase64Images extends Command
{
    protected $signature   = 'posts:convert-base64 {--dry-run : Afficher sans modifier}';
    protected $description = 'Convertit les images base64 dans description en fichiers médias';

    public function handle(): int
    {
        $posts = Post::whereRaw('description LIKE "%data:image/%"')->get();

        if ($posts->isEmpty()) {
            $this->info('Aucun post avec des images base64 trouvé.');
            return 0;
        }

        $this->info("Posts à traiter : {$posts->count()}");
        $dry = $this->option('dry-run');

        $bar = $this->output->createProgressBar($posts->count());
        $bar->start();

        foreach ($posts as $post) {
            preg_match_all('/data:image\/(.*?);base64,([A-Za-z0-9+\/=]+)/', $post->description, $matches);

            if (empty($matches[0])) {
                $bar->advance();
                continue;
            }

            $this->newLine();
            $this->line("  Post #{$post->id} — " . \Str::limit($post->title ?? '(sondage)', 50) . " : " . count($matches[0]) . " image(s), " . round(strlen($post->description) / 1024) . "KB");

            if ($dry) {
                $bar->advance();
                continue;
            }

            $updated = $post->description;

            foreach ($matches[0] as $index => $base64String) {
                try {
                    $imageType = $matches[1][$index];
                    $imageData = base64_decode($matches[2][$index]);

                    if (!$imageData) continue;

                    $fileName = 'tinymce_' . $post->id . '_' . uniqid() . '.' . $imageType;
                    Storage::disk('public')->put($fileName, $imageData);
                    $size = Storage::disk('public')->size($fileName);

                    $media = Media::create([
                        'model_type'            => Post::class,
                        'model_id'              => $post->id,
                        'name'                  => pathinfo($fileName, PATHINFO_FILENAME),
                        'file_name'             => $fileName,
                        'mime_type'             => 'image/' . $imageType,
                        'disk'                  => 'public',
                        'collection_name'       => 'tinyMceImages',
                        'size'                  => $size,
                        'manipulations'         => '[]',
                        'custom_properties'     => '[]',
                        'responsive_images'     => '[]',
                        'generated_conversions' => '{"optimized":true}',
                    ]);

                    $fileUrl = Storage::url($fileName);
                    $updated = str_replace($base64String, $fileUrl, $updated);
                } catch (\Throwable $e) {
                    $this->warn("    Erreur image #{$index}: " . $e->getMessage());
                }
            }

            $post->description = $updated;
            $post->saveQuietly(); // sans déclencher les observers (pas d'invalidation de cache en boucle)

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info($dry ? 'Dry run terminé — aucune modification.' : 'Conversion terminée !');

        return 0;
    }
}
