<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Domain\ImageSlider\Command;

use Oksydan\IsImageslider\Entity\ImageSlider;

/**
 * @see DeleteImageSliderHandler
 */
final class DeleteImageSliderCommand
{
    private ImageSlider $imageSlider;

    public function __construct(ImageSlider $imageSlider)
    {
        $this->imageSlider = $imageSlider;
    }

    public function getImageSlider(): ImageSlider
    {
        return $this->imageSlider;
    }
}
