<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Domain\ImageSlider\CommandHandler;

use Oksydan\IsImageslider\Domain\ImageSlider\Command\DeleteImageSliderImageFileCommand;
use Oksydan\IsImageslider\Helper\EraseHelper;

final class DeleteImageSliderImageFileHandler implements DeleteImageSliderImageFileHandlerInterface
{
    private EraseHelper $eraseHelper;

    public function __construct(EraseHelper $eraseHelper)
    {
        $this->eraseHelper = $eraseHelper;
    }

    public function handle(DeleteImageSliderImageFileCommand $command)
    {
        $imageSliderImage = $command->getImageSliderImage();

        $this->eraseHelper->remove($imageSliderImage->getName());
    }
}
