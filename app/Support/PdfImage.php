<?php

namespace App\Support;

class PdfImage
{
    public static function dataUri(?string $sourcePath, int $targetWidth, int $targetHeight, int $quality = 55): ?string
    {
        if (! $sourcePath || ! is_file($sourcePath) || ! extension_loaded('gd')) {
            return null;
        }

        $info = @getimagesize($sourcePath);
        if (! $info) {
            return null;
        }

        $hash = sha1($sourcePath.filemtime($sourcePath).$targetWidth.'x'.$targetHeight.'q'.$quality);
        $cacheDir = storage_path('app/pdf-cache');
        $cachePath = $cacheDir.'/'.$hash.'.jpg';

        if (! is_file($cachePath)) {
            if (! is_dir($cacheDir)) {
                mkdir($cacheDir, 0775, true);
            }

            $source = match ($info[2]) {
                IMAGETYPE_JPEG => @imagecreatefromjpeg($sourcePath),
                IMAGETYPE_PNG => @imagecreatefrompng($sourcePath),
                IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($sourcePath) : null,
                default => null,
            };

            if (! $source) {
                return null;
            }

            $srcWidth = imagesx($source);
            $srcHeight = imagesy($source);
            $scale = max($targetWidth / $srcWidth, $targetHeight / $srcHeight);
            $cropWidth = (int) round($targetWidth / $scale);
            $cropHeight = (int) round($targetHeight / $scale);
            $cropX = (int) max(0, round(($srcWidth - $cropWidth) / 2));
            $cropY = (int) max(0, round(($srcHeight - $cropHeight) / 2));

            $thumb = imagecreatetruecolor($targetWidth, $targetHeight);
            imagefill($thumb, 0, 0, imagecolorallocate($thumb, 8, 12, 24));
            imagecopyresampled($thumb, $source, 0, 0, $cropX, $cropY, $targetWidth, $targetHeight, $cropWidth, $cropHeight);
            imagejpeg($thumb, $cachePath, $quality);

            imagedestroy($source);
            imagedestroy($thumb);
        }

        return 'data:image/jpeg;base64,'.base64_encode(file_get_contents($cachePath));
    }

    public static function containDataUri(?string $sourcePath, int $targetWidth, int $targetHeight, int $quality = 60): ?string
    {
        if (! $sourcePath || ! is_file($sourcePath) || ! extension_loaded('gd')) {
            return null;
        }

        $info = @getimagesize($sourcePath);
        if (! $info) {
            return null;
        }

        $hash = sha1($sourcePath.filemtime($sourcePath).'contain'.$targetWidth.'x'.$targetHeight.'q'.$quality);
        $cacheDir = storage_path('app/pdf-cache');
        $cachePath = $cacheDir.'/'.$hash.'.jpg';

        if (! is_file($cachePath)) {
            if (! is_dir($cacheDir)) {
                mkdir($cacheDir, 0775, true);
            }

            $source = match ($info[2]) {
                IMAGETYPE_JPEG => @imagecreatefromjpeg($sourcePath),
                IMAGETYPE_PNG => @imagecreatefrompng($sourcePath),
                IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($sourcePath) : null,
                default => null,
            };

            if (! $source) {
                return null;
            }

            $srcWidth = imagesx($source);
            $srcHeight = imagesy($source);
            $scale = min($targetWidth / $srcWidth, $targetHeight / $srcHeight);
            $drawWidth = (int) round($srcWidth * $scale);
            $drawHeight = (int) round($srcHeight * $scale);
            $drawX = (int) round(($targetWidth - $drawWidth) / 2);
            $drawY = (int) round(($targetHeight - $drawHeight) / 2);

            $thumb = imagecreatetruecolor($targetWidth, $targetHeight);
            imagefill($thumb, 0, 0, imagecolorallocate($thumb, 255, 255, 255));
            imagecopyresampled($thumb, $source, $drawX, $drawY, 0, 0, $drawWidth, $drawHeight, $srcWidth, $srcHeight);
            imagejpeg($thumb, $cachePath, $quality);

            imagedestroy($source);
            imagedestroy($thumb);
        }

        return 'data:image/jpeg;base64,'.base64_encode(file_get_contents($cachePath));
    }
}
