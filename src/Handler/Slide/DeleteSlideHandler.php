<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Handler\Slide;

use Doctrine\ORM\EntityManagerInterface;
use Oksydan\IsImageslider\Cache\TemplateCache;
use Oksydan\IsImageslider\Entity\ImageSlider;
use Oksydan\IsImageslider\Handler\FileEraser;
use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShopBundle\Entity\Shop;

class DeleteSlideHandler implements SlideHandlerInterface
{
    protected Context $shopContext;

    protected EntityManagerInterface $entityManager;

    protected FileEraser $fileEraser;

    protected TemplateCache $templateCache;

    public function __construct(
        Context $shopContext,
        EntityManagerInterface $entityManager,
        FileEraser $fileEraser,
        TemplateCache $templateCache
    ) {
        $this->shopContext = $shopContext;
        $this->entityManager = $entityManager;
        $this->fileEraser = $fileEraser;
        $this->templateCache = $templateCache;
    }

    public function handle(ImageSlider $imageSlider): void
    {
        if ($this->shopContext->isAllShopContext()) {
            $imageSlider->clearShops();

            foreach ($imageSlider->getSliderLangs() as $imageSliderLang) {
                if ($imageSliderLang->getImage()) {
                    $this->eraseFile($imageSliderLang->getImage());
                }

                if ($imageSliderLang->getImageMobile()) {
                    $this->eraseFile($imageSliderLang->getImageMobile());
                }
            }

            $this->entityManager->remove($imageSlider);
        } else {
            $shopList = $this->entityManager
                ->getRepository(Shop::class)
                ->findBy(['id' => $this->shopContext->getContextListShopID()]);

            foreach ($shopList as $shop) {
                $imageSlider->removeShop($shop);
                $this->entityManager->flush();
            }

            if (count($imageSlider->getShops()) === 0) {
                $this->entityManager->remove($imageSlider);
            }
        }

        $this->entityManager->flush();
        $this->templateCache->clearTemplateCache();
    }

    protected function eraseFile(string $file): void
    {
        $this->fileEraser->remove($file);
    }
}
