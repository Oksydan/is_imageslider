<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Provider;

class ImageProvider implements ImageProviderInterface
{
    /**
     * @var string
     */
    private $imagesUri;

    public function __construct($imagesUri)
    {
        $this->imagesUri = $imagesUri;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath(string $fileName): string
    {
        return $fileName ? $this->imagesUri . $fileName : '';
    }
}
