<?php

namespace App\Support;

use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;

class PdfQr
{
    public static function dataUri(string $text, int $moduleSize = 5, int $margin = 2): ?string
    {
        if (! extension_loaded('gd')) {
            return null;
        }

        $qr = Encoder::encode($text, ErrorCorrectionLevel::M());
        $matrix = $qr->getMatrix();
        $matrixSize = $matrix->getWidth();
        $imageSize = ($matrixSize + ($margin * 2)) * $moduleSize;
        $image = imagecreatetruecolor($imageSize, $imageSize);
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);

        imagefill($image, 0, 0, $white);

        for ($y = 0; $y < $matrixSize; $y++) {
            for ($x = 0; $x < $matrixSize; $x++) {
                if ($matrix->get($x, $y) === 1) {
                    imagefilledrectangle(
                        $image,
                        ($x + $margin) * $moduleSize,
                        ($y + $margin) * $moduleSize,
                        (($x + $margin + 1) * $moduleSize) - 1,
                        (($y + $margin + 1) * $moduleSize) - 1,
                        $black
                    );
                }
            }
        }

        ob_start();
        imagepng($image, null, 6);
        $png = ob_get_clean();
        imagedestroy($image);

        return 'data:image/png;base64,'.base64_encode($png);
    }
}
