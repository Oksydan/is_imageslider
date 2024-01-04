<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Handler\Slide;

use Oksydan\IsImageslider\Entity\ImageSlider;

interface SlideHandlerInterface
{
    public function handle(ImageSlider $imageSlider): void;
}
