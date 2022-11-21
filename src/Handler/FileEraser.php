<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Handler;

class FileEraser
{
    private $targetDirectory;

    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function remove(string $fileName): bool
    {
        $result = true;
        $fullFilePath = $this->targetDirectory . $fileName;

        if (file_exists($fullFilePath)) {
            $result = unlink($fullFilePath);
        }

        return $result;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}
