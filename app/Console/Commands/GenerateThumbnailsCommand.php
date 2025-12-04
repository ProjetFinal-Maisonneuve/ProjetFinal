<?php

namespace App\Console\Commands;

use App\Models\BouteilleCatalogue;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class GenerateThumbnailsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'saq:generate-thumbnails 
                            {--limit= : Nombre maximum de produits à traiter}
                            {--force : Régénérer même si le thumbnail existe déjà}
                            {--size=300 : Taille maximale du thumbnail en pixels}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Génère les thumbnails pour les bouteilles existantes depuis leurs images';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Démarrage de la génération des thumbnails...');

        $limit = $this->option('limit') ? (int) $this->option('limit') : null;
        $force = $this->option('force');
        $maxSize = (int) $this->option('size');

        // Vérifier que GD est disponible
        if (!function_exists('imagecreatefromjpeg') && !function_exists('imagecreatefrompng')) {
            $this->error('L\'extension GD n\'est pas installée. Veuillez installer php-gd.');
            return Command::FAILURE;
        }

        // Trouver les produits qui ont une image mais pas de thumbnail
        $query = BouteilleCatalogue::whereNotNull('url_image')
            ->where(function ($q) use ($force) {
                if (!$force) {
                    $q->whereNull('url_image_thumbnail')
                      ->orWhere('url_image_thumbnail', '');
                }
            });

        $totalCount = $query->count();
        
        if ($totalCount === 0) {
            $this->info('Aucun produit à traiter. Tous les produits ont déjà un thumbnail.');
            return Command::SUCCESS;
        }

        $query = $query->orderBy('id');

        if ($limit) {
            $query->limit($limit);
            $this->info("Traitement de {$limit} produits sur {$totalCount}...");
        } else {
            $this->info("Traitement de {$totalCount} produits...");
        }

        $bouteilles = $query->get();
        $successCount = 0;
        $errorCount = 0;
        $skippedCount = 0;

        $bar = $this->output->createProgressBar($bouteilles->count());
        $bar->start();

        foreach ($bouteilles as $bouteille) {
            try {
                $result = $this->generateThumbnail($bouteille, $maxSize, $force);
                
                if ($result === 'success') {
                    $successCount++;
                } elseif ($result === 'skipped') {
                    $skippedCount++;
                } else {
                    $errorCount++;
                }
            } catch (\Exception $e) {
                $errorCount++;
                Log::error("Erreur lors de la génération du thumbnail pour bouteille {$bouteille->id}: " . $e->getMessage());
            }
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Génération terminée !");
        $this->info("- Succès: {$successCount}");
        $this->info("- Ignorés: {$skippedCount}");
        if ($errorCount > 0) {
            $this->warn("- Erreurs: {$errorCount}");
        }

        return Command::SUCCESS;
    }

    /**
     * Génère un thumbnail pour une bouteille
     *
     * @param BouteilleCatalogue $bouteille
     * @param int $maxSize Taille maximale du thumbnail
     * @param bool $force Régénérer même si existe
     * @return string 'success', 'skipped', ou 'error'
     */
    private function generateThumbnail(BouteilleCatalogue $bouteille, int $maxSize, bool $force): string
    {
        if (empty($bouteille->url_image)) {
            return 'skipped';
        }

        // Normaliser le chemin de l'image
        $imagePath = ltrim($bouteille->url_image, '/');
        
        // Enlever les préfixes storage/ répétés
        while (str_starts_with($imagePath, 'storage/')) {
            $imagePath = substr($imagePath, 8);
        }

        // Vérifier si l'image existe
        if (!Storage::disk('public')->exists($imagePath)) {
            // Si c'est une URL externe, on skip
            if (str_starts_with($imagePath, 'http')) {
                return 'skipped';
            }
            Log::warning("Image introuvable pour bouteille {$bouteille->id}: {$imagePath}");
            return 'error';
        }

        // Déterminer le chemin du thumbnail
        $pathInfo = pathinfo($imagePath);
        $thumbnailPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_thumb.' . ($pathInfo['extension'] ?? 'jpg');

        // Vérifier si le thumbnail existe déjà
        if (!$force && Storage::disk('public')->exists($thumbnailPath)) {
            // Mettre à jour la BDD si le chemin n'est pas enregistré
            if (empty($bouteille->url_image_thumbnail)) {
                $bouteille->update(['url_image_thumbnail' => $thumbnailPath]);
            }
            return 'skipped';
        }

        // Lire l'image source
        $fullImagePath = Storage::disk('public')->path($imagePath);
        
        // Détecter le type d'image
        $imageInfo = getimagesize($fullImagePath);
        if ($imageInfo === false) {
            Log::error("Impossible de lire l'image pour bouteille {$bouteille->id}: {$fullImagePath}");
            return 'error';
        }

        $mimeType = $imageInfo['mime'];
        $sourceWidth = $imageInfo[0];
        $sourceHeight = $imageInfo[1];

        // Créer l'image source selon le type
        $sourceImage = null;
        switch ($mimeType) {
            case 'image/jpeg':
                $sourceImage = imagecreatefromjpeg($fullImagePath);
                break;
            case 'image/png':
                $sourceImage = imagecreatefrompng($fullImagePath);
                break;
            case 'image/gif':
                $sourceImage = imagecreatefromgif($fullImagePath);
                break;
            case 'image/webp':
                if (function_exists('imagecreatefromwebp')) {
                    $sourceImage = imagecreatefromwebp($fullImagePath);
                }
                break;
            default:
                Log::warning("Type d'image non supporté pour bouteille {$bouteille->id}: {$mimeType}");
                return 'error';
        }

        if ($sourceImage === false) {
            Log::error("Impossible de charger l'image pour bouteille {$bouteille->id}");
            return 'error';
        }

        // Calculer les nouvelles dimensions en préservant le ratio
        $ratio = min($maxSize / $sourceWidth, $maxSize / $sourceHeight);
        $newWidth = (int) ($sourceWidth * $ratio);
        $newHeight = (int) ($sourceHeight * $ratio);

        // Si l'image est déjà plus petite que maxSize, on peut juste la copier
        if ($newWidth >= $sourceWidth && $newHeight >= $sourceHeight) {
            // L'image source est déjà assez petite, on peut la copier directement
            $thumbnailImage = $sourceImage;
        } else {
            // Créer une nouvelle image redimensionnée
            $thumbnailImage = imagecreatetruecolor($newWidth, $newHeight);

            // Préserver la transparence pour PNG
            if ($mimeType === 'image/png') {
                imagealphablending($thumbnailImage, false);
                imagesavealpha($thumbnailImage, true);
                $transparent = imagecolorallocatealpha($thumbnailImage, 255, 255, 255, 127);
                imagefilledrectangle($thumbnailImage, 0, 0, $newWidth, $newHeight, $transparent);
            }

            // Redimensionner avec une meilleure qualité
            imagecopyresampled(
                $thumbnailImage,
                $sourceImage,
                0, 0, 0, 0,
                $newWidth, $newHeight,
                $sourceWidth, $sourceHeight
            );
        }

        // Sauvegarder le thumbnail
        $thumbnailFullPath = Storage::disk('public')->path($thumbnailPath);
        
        $saved = false;
        $extension = strtolower($pathInfo['extension'] ?? 'jpg');
        
        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                $saved = imagejpeg($thumbnailImage, $thumbnailFullPath, 85);
                break;
            case 'png':
                $saved = imagepng($thumbnailImage, $thumbnailFullPath, 8);
                break;
            case 'gif':
                $saved = imagegif($thumbnailImage, $thumbnailFullPath);
                break;
            case 'webp':
                if (function_exists('imagewebp')) {
                    $saved = imagewebp($thumbnailImage, $thumbnailFullPath, 85);
                }
                break;
        }

        // Libérer la mémoire
        if ($thumbnailImage !== $sourceImage) {
            imagedestroy($thumbnailImage);
        }
        imagedestroy($sourceImage);

        if (!$saved) {
            Log::error("Impossible de sauvegarder le thumbnail pour bouteille {$bouteille->id}: {$thumbnailPath}");
            return 'error';
        }

        // Mettre à jour la base de données
        $bouteille->update(['url_image_thumbnail' => $thumbnailPath]);

        return 'success';
    }
}
