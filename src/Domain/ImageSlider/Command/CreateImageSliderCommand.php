<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Domain\ImageSlider\Command;

use Oksydan\IsImageslider\Entity\ImageSlider;

/**
 * @see CreateImageSliderCommandHandler
 */
final class CreateImageSliderCommand
{
    private ImageSlider $imageSlider;

    private array $files;

    public function __construct(ImageSlider $imageSlider, array $files = [])
    {
        $this->imageSlider = $imageSlider;
        $this->files = $files;
    }

    public function getImageSlider(): ImageSlider
    {
        return $this->imageSlider;
    }

    public function getFiles(): array
    {
        return $this->files;
    }
}
