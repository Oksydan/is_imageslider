<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Handler\Slide;

use Doctrine\ORM\EntityManagerInterface;
use Oksydan\IsImageslider\Cache\TemplateCache;
use Oksydan\IsImageslider\Entity\ImageSlider;

class ToggleSlideActivityHandler implements SlideHandlerInterface
{
    protected EntityManagerInterface $entityManager;

    protected TemplateCache $templateCache;

    /**
     * @param EntityManagerInterface $entityManager
     * @param TemplateCache $templateCache
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        TemplateCache $templateCache
    ) {
        $this->entityManager = $entityManager;
        $this->templateCache = $templateCache;
    }

    public function handle(ImageSlider $imageSlider): void
    {
        $imageSlider->setActive(!$imageSlider->getActive());

        $this->entityManager->persist($imageSlider);
        $this->entityManager->flush();
        $this->templateCache->clearTemplateCache();
    }
}
