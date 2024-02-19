<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Domain\ImageSlider\CommandHandler;

use Oksydan\IsImageslider\Domain\ImageSlider\Command\EditImageSliderCommand;
use Oksydan\IsImageslider\Repository\ImageSliderRepository;

final class EditImageSliderHandler implements EditImageSliderHandlerInterface
{
    private ImageSliderRepository $imageSliderRepository;

    public function __construct(ImageSliderRepository $imageSliderRepository)
    {
        $this->imageSliderRepository = $imageSliderRepository;
    }

    public function handle(EditImageSliderCommand $command): void
    {
        $imageSlider = $command->getImageSlider();
        $files = $command->getFiles();

        $this->imageSliderRepository->save($imageSlider);
    }
}
