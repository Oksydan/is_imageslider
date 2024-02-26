<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Domain\ImageSlider\CommandHandler;

use Doctrine\Common\Collections\Collection;
use Oksydan\IsImageslider\Entity\ImageSliderImage;
use Oksydan\IsImageslider\Entity\ImageSliderLang;
use Symfony\Component\HttpFoundation\File\UploadedFile;

trait UploadFilesTrait
{
    public function uploadFile(UploadedFile $file): ImageSliderImage
    {
        $image = new ImageSliderImage();

        $fileName = $this->uploadHelper->uploadImage($file);
        $image->setName($fileName);

        return $image;
    }

    public function saveImagesToSliderLang(array $files, Collection $sliderLangs): void
    {
        foreach ($sliderLangs as $index => $sliderLang) {
            $langFiles = $files[$index] ?? [];

            foreach ($langFiles as $name => $langFile) {
                $file = $langFile['image'];
                $name = str_replace('_', '', ucwords($name, '_'));
                $setter = 'set' . $name;

                if (null === $file) {
                    continue;
                }

                $imageSliderImage = $this->uploadFile($file);

                if (method_exists($sliderLang, $setter)) {
                    $sliderLang->$setter($imageSliderImage);
                }
            }
        }
    }
}
