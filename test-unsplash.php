<?php

// Test d'ajout d'image depuis Unsplash

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Post;

$post = Post::latest()->first();

if (!$post) {
    echo "Aucun post trouvé\n";
    exit(1);
}

echo "Test avec le post: {$post->title}\n";

try {
    // Utiliser picsum.photos qui est plus stable
    $imageUrl = "https://picsum.photos/800/600?random=" . time();
    echo "URL: $imageUrl\n";
    
    // Télécharger l'image
    $imageContent = file_get_contents($imageUrl);
    echo "Taille image: " . strlen($imageContent) . " bytes\n";
    
    if ($imageContent !== false && strlen($imageContent) > 0) {
        // Créer un fichier temporaire
        $tempFile = tempnam(sys_get_temp_dir(), 'unsplash_');
        file_put_contents($tempFile, $imageContent);
        echo "Fichier temporaire: $tempFile\n";
        
        // Ajouter l'image avec Spatie
        $post->addMedia($tempFile)
            ->usingFileName('test_' . time() . '.jpg')
            ->toMediaCollection('image');
        
        // Supprimer le fichier temporaire
        unlink($tempFile);
        
        echo "✓ Image ajoutée avec succès!\n";
        echo "Nombre d'images du post: " . $post->media->count() . "\n";
    } else {
        echo "✗ Échec du téléchargement\n";
    }
} catch (\Exception $e) {
    echo "✗ Erreur: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
