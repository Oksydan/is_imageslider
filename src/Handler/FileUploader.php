<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Handler;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private string $imagesDir;

    /**
     * @param string $imagesDir
     */
    public function __construct(string $imagesDir)
    {
        $this->imagesDir = $imagesDir;
    }

    public function upload(UploadedFile $file)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $fileName = md5($originalFilename . time() . uniqid()) . '.' . $file->guessExtension();

        $this->createUploadDirectoryIfNotExists();

        try {
            $file->move($this->getTargetDirectory(), $fileName);
        } catch (FileException $e) {
        }

        return $fileName;
    }

    private function createUploadDirectoryIfNotExists()
    {
        if (!file_exists($this->getTargetDirectory())) {
            mkdir($this->getTargetDirectory(), 0755, true);
        }
    }

    public function getTargetDirectory()
    {
        return $this->imagesDir;
    }
}
