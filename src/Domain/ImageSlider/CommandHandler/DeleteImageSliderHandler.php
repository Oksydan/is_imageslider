<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Domain\ImageSlider\CommandHandler;

use Oksydan\IsImageslider\Domain\ImageSlider\Command\DeleteImageSliderCommand;
use Oksydan\IsImageslider\Domain\ImageSlider\Command\DeleteImageSliderImageFileCommand;
use Oksydan\IsImageslider\Entity\ImageSliderImage;
use Oksydan\IsImageslider\Helper\EraseHelper;
use Oksydan\IsImageslider\Repository\ImageSliderRepository;

final class DeleteImageSliderHandler implements DeleteImageSliderHandlerInterface
{
    private ImageSliderRepository $imageSliderRepository;

    private EraseHelper $eraseHelper;

    public function __construct(
        ImageSliderRepository $imageSliderRepository,
        EraseHelper $eraseHelper
    ) {
        $this->imageSliderRepository = $imageSliderRepository;
        $this->eraseHelper = $eraseHelper;
    }

    public function handle(DeleteImageSliderCommand $command)
    {
        $imageSlider = $command->getImageSlider();

        $sliderLangs = $imageSlider->getSliderLangs();

        foreach ($sliderLangs as $sliderLang) {
            if (null !== $sliderLang->getImage()) {
                $this->deleteImage($sliderLang->getImage());
            }

            if (null !== $sliderLang->getImageMobile()) {
                $this->deleteImage($sliderLang->getImageMobile());
            }
        }

        $this->imageSliderRepository->delete($imageSlider);
    }

    private function deleteImage(ImageSliderImage $image)
    {
        $this->eraseHelper->remove($image->getName());
    }
}
