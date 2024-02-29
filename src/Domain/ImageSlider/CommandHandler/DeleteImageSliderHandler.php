<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Domain\ImageSlider\CommandHandler;

use Oksydan\IsImageslider\Domain\ImageSlider\Command\DeleteImageSliderCommand;
use Oksydan\IsImageslider\Repository\ImageSliderRepository;

final class DeleteImageSliderHandler implements DeleteImageSliderHandlerInterface
{
    private ImageSliderRepository $imageSliderRepository;

    public function __construct(ImageSliderRepository $imageSliderRepository)
    {
        $this->imageSliderRepository = $imageSliderRepository;
    }

    public function handle(DeleteImageSliderCommand $command)
    {
        $imageSlider = $command->getImageSlider();
        $this->imageSliderRepository->delete($imageSlider);
    }
}
