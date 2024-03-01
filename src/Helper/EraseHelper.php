<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Helper;

class EraseHelper
{
    private string $imagesDir;

    /**
     * @param string $imagesDir
     */
    public function __construct(string $imagesDir)
    {
        $this->imagesDir = $imagesDir;
    }

    public function remove(string $fileName): bool
    {
        $result = true;
        $fullFilePath = $this->imagesDir . $fileName;

        if (file_exists($fullFilePath)) {
            $result = unlink($fullFilePath);
        }

        $result = $result && $this->removeWebpFile($fileName);

        return $result && $this->removeAvifFile($fileName);
    }

    private function removeWebpFile(string $fileName): bool
    {
        $webpFileName = pathinfo($fileName, PATHINFO_FILENAME) . '.webp';
        $fullFilePath = $this->imagesDir . $webpFileName;

        if (file_exists($fullFilePath)) {
            return unlink($fullFilePath);
        }

        return true;
    }

    private function removeAvifFile(string $fileName): bool
    {
        $avifFileName = pathinfo($fileName, PATHINFO_FILENAME) . '.avif';
        $fullFilePath = $this->imagesDir . $avifFileName;

        if (file_exists($fullFilePath)) {
            return unlink($fullFilePath);
        }

        return true;
    }
}
