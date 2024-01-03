<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Handler;

class FileEraser
{
    private string $imagesDir;

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

        return $result;
    }

    public function getTargetDirectory()
    {
        return $this->imagesDir;
    }
}
