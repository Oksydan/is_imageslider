<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Provider;

use Oksydan\IsImageslider\Entity\ImageSliderImage;
use Oksydan\IsImageslider\Repository\ImageSliderImageRepository;

class ImageProvider implements ImageProviderInterface
{
    private string $imagesUri;

    private string $placeholderImage;

    private ImageSliderImageRepository $imageSliderImageRepository;

    /**
     * @param string $imagesUri
     */
    public function __construct(
        string $imagesUri,
        string $placeholderImage,
        ImageSliderImageRepository $imageSliderImageRepository
    ) {
        $this->imagesUri = $imagesUri;
        $this->placeholderImage = $placeholderImage;
        $this->imageSliderImageRepository = $imageSliderImageRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath(?int $imageId): string
    {
        if (!$imageId) {
            return $this->getPlaceholder();
        }

        /* @var $image ImageSliderImage */
        $image = $this->imageSliderImageRepository->find($imageId);

        if (null === $image) {
            return $this->getPlaceholder();
        }

        return $this->imagesUri . $image->getName();
    }

    private function getPlaceholder(): string
    {
        return $this->placeholderImage;
    }
}
