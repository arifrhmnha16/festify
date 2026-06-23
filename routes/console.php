<?php

use App\Models\Concert;
use App\Support\PosterImage;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('posters:webp {--delete-originals : Delete old jpg/png poster files after conversion}', function () {
    $converted = 0;
    $skipped = 0;
    $convertDirectory = function (string $directory) use (&$converted, &$skipped) {
        foreach (glob($directory.'/*.{jpg,jpeg,png}', GLOB_BRACE) ?: [] as $sourcePath) {
            $destinationPath = dirname($sourcePath).'/'.pathinfo($sourcePath, PATHINFO_FILENAME).'.webp';

            if (is_file($destinationPath)) {
                $skipped++;

                continue;
            }

            PosterImage::convertToWebp($sourcePath, $destinationPath);
            $converted++;
        }
    };

    $convertDirectory(storage_path('app/public/posters'));
    $convertDirectory(public_path('posters'));

    try {
        Concert::query()
            ->whereNotNull('poster')
            ->each(function (Concert $concert) use (&$converted, &$skipped) {
                if (strtolower(pathinfo($concert->poster, PATHINFO_EXTENSION)) === 'webp') {
                    $skipped++;

                    return;
                }

                $basename = basename($concert->poster);
                $publicPath = public_path('posters/'.$basename);
                $storagePath = Storage::disk('public')->path($concert->poster);
                $sourcePath = is_file($storagePath) ? $storagePath : $publicPath;

                if (! is_file($sourcePath)) {
                    $skipped++;

                    return;
                }

                $webpRelativePath = 'posters/'.pathinfo($basename, PATHINFO_FILENAME).'.webp';

                if (! Storage::disk('public')->exists($webpRelativePath)) {
                    PosterImage::convertToPublicDisk($sourcePath, $webpRelativePath);
                    $converted++;
                }

                if (is_file($publicPath) && ! is_file(public_path($webpRelativePath))) {
                    File::ensureDirectoryExists(public_path('posters'));
                    PosterImage::convertToWebp($sourcePath, public_path($webpRelativePath));
                    $converted++;
                }

                $concert->update(['poster' => $webpRelativePath]);
            });
    } catch (Throwable $exception) {
        $this->warn('Database is unavailable, so poster columns were not updated. WebP files were still generated.');
    }

    $this->info("Converted {$converted} poster(s) to WebP. Skipped {$skipped}.");

    if ($this->option('delete-originals')) {
        foreach (glob(storage_path('app/public/posters/*.{jpg,jpeg,png}'), GLOB_BRACE) ?: [] as $path) {
            File::delete($path);
        }

        foreach (glob(public_path('posters/*.{jpg,jpeg,png}'), GLOB_BRACE) ?: [] as $path) {
            File::delete($path);
        }

        $this->info('Old jpg/png poster files deleted.');
    }
})->purpose('Convert existing concert posters to WebP');
