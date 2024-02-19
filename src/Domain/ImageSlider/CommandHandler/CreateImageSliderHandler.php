<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Domain\ImageSlider\CommandHandler;

use Oksydan\IsImageslider\Repository\ImageSliderRepository;
use Oksydan\IsImageslider\Domain\ImageSlider\Command\CreateImageSliderCommand;

final class CreateImageSliderHandler implements CreateImageSliderHandlerInterface
{
    private ImageSliderRepository $imageSliderRepository;

    public function __construct(ImageSliderRepository $imageSliderRepository)
    {
        $this->imageSliderRepository = $imageSliderRepository;
    }

    public function handle(CreateImageSliderCommand $command): void
    {
        $imageSlider = $command->getImageSlider();
        $files = $command->getFiles();

        $imageSlider->setPosition($this->imageSliderRepository->getHighestPosition() + 1);

        $this->imageSliderRepository->save($imageSlider);
    }
}
