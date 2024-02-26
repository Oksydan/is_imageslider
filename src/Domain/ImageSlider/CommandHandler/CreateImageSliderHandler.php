<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Domain\ImageSlider\CommandHandler;

use Oksydan\IsImageslider\Domain\ImageSlider\Command\CreateImageSliderCommand;
use Oksydan\IsImageslider\Helper\UploadHelper;
use Oksydan\IsImageslider\Repository\ImageSliderRepository;

final class CreateImageSliderHandler implements CreateImageSliderHandlerInterface
{
    use UploadFilesTrait;

    private ImageSliderRepository $imageSliderRepository;

    private UploadHelper $uploadHelper;

    public function __construct(ImageSliderRepository $imageSliderRepository, UploadHelper $uploadHelper)
    {
        $this->imageSliderRepository = $imageSliderRepository;
        $this->uploadHelper = $uploadHelper;
    }

    public function handle(CreateImageSliderCommand $command): void
    {
        $imageSlider = $command->getImageSlider();
        $files = $command->getFiles();

        $imageSlider->setPosition($this->imageSliderRepository->getHighestPosition() + 1);
        $this->saveImagesToSliderLang($files, $imageSlider->getSliderLangs());

        $this->imageSliderRepository->save($imageSlider);
    }
}
