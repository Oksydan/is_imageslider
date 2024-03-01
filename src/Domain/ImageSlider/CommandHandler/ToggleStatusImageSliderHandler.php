<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Domain\ImageSlider\CommandHandler;

use Oksydan\IsImageslider\Domain\ImageSlider\Command\ToggleStatusImageSliderCommand;
use Oksydan\IsImageslider\Repository\ImageSliderRepository;

final class ToggleStatusImageSliderHandler implements ToggleStatusImageSliderHandlerInterface
{
    private ImageSliderRepository $imageSliderRepository;

    public function __construct(ImageSliderRepository $imageSliderRepository)
    {
        $this->imageSliderRepository = $imageSliderRepository;
    }

    public function handle(ToggleStatusImageSliderCommand $command)
    {
        $imageSlider = $command->getImageSlider();
        $imageSlider->setActive(!$imageSlider->getActive());

        $this->imageSliderRepository->save($imageSlider);
    }
}
