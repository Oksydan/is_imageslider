<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Domain\ImageSlider\EventListener;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use Oksydan\IsImageslider\Adapter\CommandBusInterface;
use Oksydan\IsImageslider\Domain\ImageSlider\Command\DeleteImageSliderImageFileCommand;
use Oksydan\IsImageslider\Entity\ImageSliderImage;

class ImageSliderImageEventListener
{
    private CommandBusInterface $commandBus;

    public function __construct(CommandBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function postRemove(ImageSliderImage $imageSliderImage, LifecycleEventArgs $args): void
    {
        $this->commandBus->handle(new DeleteImageSliderImageFileCommand($imageSliderImage));
    }
}
