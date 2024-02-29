<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Domain\ImageSlider\Command;

use Oksydan\IsImageslider\Entity\ImageSliderImage;

/**
 * @see DeleteImageSliderImageFileHandler
 */
final class DeleteImageSliderImageFileCommand
{
    private ImageSliderImage $imageSliderImage;

    public function __construct(ImageSliderImage $imageSliderImage)
    {
        $this->imageSliderImage = $imageSliderImage;
    }

    public function getImageSliderImage(): ImageSliderImage
    {
        return $this->imageSliderImage;
    }
}
