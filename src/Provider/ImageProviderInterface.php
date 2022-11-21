<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Provider;

interface ImageProviderInterface
{
    /**
     * Get slider image path.
     *
     * @param string $fileName
     *
     * @return string Path to slider image
     */
    public function getPath(string $fileName);
}
