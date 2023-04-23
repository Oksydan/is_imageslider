<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Presenter;

class ImageSlidePresenter
{
    /**
     * @var string
     */
    private $imagesUri;

    /**
     * @var string
     */
    private $imagesDir;

    /**
     * @var \Context
     */
    private $context;

    public function __construct(
        string $imagesUri,
        string $imagesDir,
        \Context $context
    ) {
        $this->imagesUri = $imagesUri;
        $this->imagesDir = $imagesDir;
        $this->context = $context;
    }

    public function present($slide): array
    {
        $imageField = $this->context->isMobile() ? 'imageMobile' : 'image';

        $slide['image_url'] = $this->getImageUrl($slide[$imageField]);
        $slide['sizes'] = $this->getImageSizes($slide[$imageField]);

        return $slide;
    }

    private function getImageUrl($slideImage): string
    {
        return $this->context->link->getMediaLink($this->imagesUri . $slideImage);
    }

    private function getImageSizes($slideImage): array
    {
        $imageFullPath = $this->imagesDir . $slideImage;
        $sizes = [];

        if (file_exists($imageFullPath)) {
            $sizes = getimagesize($imageFullPath);
        }

        return $sizes;
    }
}
