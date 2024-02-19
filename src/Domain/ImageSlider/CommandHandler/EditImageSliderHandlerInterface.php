<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Domain\ImageSlider\CommandHandler;

use Oksydan\IsImageslider\Domain\ImageSlider\Command\EditImageSliderCommand;

interface EditImageSliderHandlerInterface
{
    public function handle(EditImageSliderCommand $command);
}
