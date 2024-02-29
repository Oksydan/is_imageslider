<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Domain\ImageSlider\CommandHandler;

use Oksydan\IsImageslider\Domain\ImageSlider\Command\ToggleStatusImageSliderCommand;

interface ToggleStatusImageSliderHandlerInterface
{
    public function handle(ToggleStatusImageSliderCommand $command);
}
