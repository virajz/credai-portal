<?php

namespace App\Services;

use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\Fill;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class QrCodeService
{
    public function __construct(
        protected int $size = 600,
        protected int $margin = 4
    ) {}

    /**
     * Generate QR code SVG from a URL or string.
     */
    public function generate(string $content): string
    {
        $svg = (new Writer(
            new ImageRenderer(
                new RendererStyle(
                    $this->size,
                    $this->margin,
                    null,
                    null,
                    Fill::uniformColor(new Rgb(255, 255, 255), new Rgb(0, 0, 0))
                ),
                new SvgImageBackEnd
            )
        ))->writeString($content);

        return trim(substr($svg, strpos($svg, "\n") + 1));
    }

    /**
     * Generate QR code PNG image resource from a URL or string.
     */
    public function generatePng(string $content): \GdImage
    {
        $pngData = (new Writer(
            new ImageRenderer(
                new RendererStyle(
                    $this->size,
                    $this->margin,
                    null,
                    null,
                    Fill::uniformColor(new Rgb(255, 255, 255), new Rgb(0, 0, 0))
                ),
                new ImagickImageBackEnd
            )
        ))->writeString($content);

        $image = imagecreatefromstring($pngData);

        if ($image === false) {
            throw new \RuntimeException('Failed to create image from QR code data');
        }

        return $image;
    }

    /**
     * Overlay QR code onto the WhatsApp base image.
     */
    public function overlayOnWhatsAppImage(string $content, int $x = 280, int $y = 980): string
    {
        $baseImagePath = public_path('whatsapp_qr.png');

        if (! file_exists($baseImagePath)) {
            throw new \RuntimeException('WhatsApp base image not found at: '.$baseImagePath);
        }

        $baseImage = imagecreatefrompng($baseImagePath);

        if ($baseImage === false) {
            throw new \RuntimeException('Failed to load WhatsApp base image');
        }

        $qrImage = $this->generatePng($content);

        $qrWidth = imagesx($qrImage);
        $qrHeight = imagesy($qrImage);

        imagecopy($baseImage, $qrImage, $x, $y, 0, 0, $qrWidth, $qrHeight);

        ob_start();
        imagepng($baseImage);
        $imageData = ob_get_clean();

        imagedestroy($baseImage);
        imagedestroy($qrImage);

        if ($imageData === false) {
            throw new \RuntimeException('Failed to generate final image');
        }

        return $imageData;
    }

    /**
     * Save WhatsApp QR image and return public URL.
     */
    public function saveWhatsAppQrImage(string $content, string $filename, int $x = 280, int $y = 980): string
    {
        $imageData = $this->overlayOnWhatsAppImage($content, $x, $y);

        $staticUrl = config('whatsapp.local_url');

        if ($staticUrl) {
            return $staticUrl;
        }

        $disk = config('filesystems.default');
        $path = 'qr-codes/'.$filename.'.png';

        \Illuminate\Support\Facades\Storage::disk($disk)->put($path, $imageData);

        return \Illuminate\Support\Facades\Storage::disk($disk)->url($path);
    }

    /**
     * Generate QR code with custom size and margin.
     */
    public function withSize(int $size, int $margin = 4): self
    {
        return new self($size, $margin);
    }
}
