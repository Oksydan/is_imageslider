<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Helper;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadHelper
{
    private string $imagesDir;

    public function __construct(string $imagesDir)
    {
        $this->imagesDir = $imagesDir;
    }

    /**
     * @throws FileException
     */
    public function uploadImage(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        $newFilename = md5($originalFilename . '-' . uniqid()) . '.' . $file->guessExtension();

        $file->move($this->imagesDir, $newFilename);

        return $newFilename;
    }
}
