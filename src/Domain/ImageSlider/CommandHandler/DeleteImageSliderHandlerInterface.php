<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Domain\ImageSlider\CommandHandler;

use Oksydan\IsImageslider\Domain\ImageSlider\Command\DeleteImageSliderCommand;

interface DeleteImageSliderHandlerInterface
{
    public function handle(DeleteImageSliderCommand $command);
}
