<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Domain\ImageSlider\CommandHandler;

use Oksydan\IsImageslider\Domain\ImageSlider\Command\DeleteImageSliderImageFileCommand;

interface DeleteImageSliderImageFileHandlerInterface
{
    public function handle(DeleteImageSliderImageFileCommand $command);
}
