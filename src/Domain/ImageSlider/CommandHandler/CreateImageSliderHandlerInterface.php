<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Domain\ImageSlider\CommandHandler;

use Oksydan\IsImageslider\Domain\ImageSlider\Command\CreateImageSliderCommand;

interface CreateImageSliderHandlerInterface
{
    public function handle(CreateImageSliderCommand $command);
}
