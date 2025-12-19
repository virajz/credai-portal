<?php

namespace App\Services;

use BaconQrCode\Renderer\Color\Rgb;
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
     * Generate QR code with custom size and margin.
     */
    public function withSize(int $size, int $margin = 4): self
    {
        return new self($size, $margin);
    }
}
