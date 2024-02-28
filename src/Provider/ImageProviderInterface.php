<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Provider;

interface ImageProviderInterface
{
    /**
     * @param int|null $fileName
     *
     * @return string Path to slider image
     */
    public function getPath(?int $imageId): string;
}
