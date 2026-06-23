<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class PosterImage
{
    public const MAX_WIDTH = 1920;

    public const QUALITY = 82;

    public static function storeWebp(UploadedFile $file, string $directory = 'posters'): string
    {
        $relativePath = trim($directory, '/').'/'.Str::random(40).'.webp';

        static::convertToPublicDisk($file->getRealPath(), $relativePath);

        return $relativePath;
    }

    public static function convertToPublicDisk(string $sourcePath, string $relativePath): void
    {
        $disk = Storage::disk('public');
        $directory = dirname($relativePath);

        if (! $disk->exists($directory)) {
            $disk->makeDirectory($directory);
        }

        static::convertToWebp($sourcePath, $disk->path($relativePath));
    }

    public static function convertToWebp(string $sourcePath, string $destinationPath): void
    {
        $source = @imagecreatefromstring((string) file_get_contents($sourcePath));

        if (! $source) {
            throw new RuntimeException('Poster tidak bisa dibaca sebagai gambar.');
        }

        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);
        $targetWidth = min($sourceWidth, self::MAX_WIDTH);
        $targetHeight = (int) round($sourceHeight * ($targetWidth / $sourceWidth));

        $target = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($target, false);
        imagesavealpha($target, true);

        $transparent = imagecolorallocatealpha($target, 255, 255, 255, 127);
        imagefilledrectangle($target, 0, 0, $targetWidth, $targetHeight, $transparent);

        imagecopyresampled(
            $target,
            $source,
            0,
            0,
            0,
            0,
            $targetWidth,
            $targetHeight,
            $sourceWidth,
            $sourceHeight
        );

        if (! imagewebp($target, $destinationPath, self::QUALITY)) {
            imagedestroy($source);
            imagedestroy($target);

            throw new RuntimeException('Poster gagal dikonversi ke WebP.');
        }

        imagedestroy($source);
        imagedestroy($target);
    }
}
