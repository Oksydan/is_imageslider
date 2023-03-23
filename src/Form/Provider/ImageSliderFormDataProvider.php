<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Form\Provider;

use Doctrine\ORM\EntityRepository;
use Oksydan\IsImageslider\Provider\ImageProviderInterface;
use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\FormDataProviderInterface;
use PrestaShopBundle\Entity\Repository\LangRepository;

class ImageSliderFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var EntityRepository
     */
    private $repository;

    /**
     * @var ImageProviderInterface
     */
    private $imagesliderImageThumbProvider;

    /**
     * @var LangRepository
     */
    private $langRepository;

    /**
     * @var string
     */
    private $placeholderImage;

    /**
     * @var Context
     */
    private $shopContext;

    /**
     * ImageSliderFormDataProvider constructor.
     *
     * @param EntityRepository $repository
     */
    public function __construct(
        EntityRepository $repository,
        ImageProviderInterface $imagesliderImageThumbProvider,
        LangRepository $langRepository,
        string $placeholderImage,
        Context $shopContext
    ) {
        $this->repository = $repository;
        $this->shopContext = $shopContext;
        $this->imagesliderImageThumbProvider = $imagesliderImageThumbProvider;
        $this->placeholderImage = $placeholderImage;
        $this->langRepository = $langRepository;
    }

    /**
     * @param mixed $id
     *
     * @return array
     */
    public function getData($id): array
    {
        $imageSlide = $this->repository->findOneById((int) $id);

        $shopIds = [];
        $slideData = [];

        foreach ($imageSlide->getShops() as $shop) {
            $shopIds[] = $shop->getId();
        }

        $slideData['shop_association'] = $shopIds;
        $slideData['active'] = $imageSlide->getActive();
        $slideData['display_from'] = $imageSlide->getDisplayFrom();
        $slideData['display_to'] = $imageSlide->getDisplayTo();

        foreach ($imageSlide->getSliderLangs() as $imageSlideLang) {
            $slideData['title'][$imageSlideLang->getLang()->getId()] = $imageSlideLang->getTitle();
            $slideData['legend'][$imageSlideLang->getLang()->getId()] = $imageSlideLang->getLegend();
            $slideData['url'][$imageSlideLang->getLang()->getId()] = $imageSlideLang->getUrl();
            $slideData['description'][$imageSlideLang->getLang()->getId()] = $imageSlideLang->getDescription();
            $slideData['image'][$imageSlideLang->getLang()->getId()] = $imageSlideLang->getImage();
            $slideData['image_mobile'][$imageSlideLang->getLang()->getId()] = $imageSlideLang->getImageMobile();

            $slideData['image_preview'][$imageSlideLang->getLang()->getId()] = $this->imagesliderImageThumbProvider->getPath($imageSlideLang->getImage()) ?? $this->placeholderImage;
            $slideData['image_mobile_preview'][$imageSlideLang->getLang()->getId()] = $this->imagesliderImageThumbProvider->getPath($imageSlideLang->getImageMobile()) ?? $this->placeholderImage;
        }

        return $slideData;
    }

    /**
     * @return array
     */
    private function getImagePreviewPlaceholder(): array
    {
        $imagePreview = [];
        $languages = $this->langRepository->findBy(['active' => true]);

        foreach ($languages as $lang) {
            $imagePreview[$lang->getId()] = $this->placeholderImage;
        }

        return $imagePreview;
    }

    /**
     * @return array
     */
    public function getDefaultData(): array
    {
        return [
            'image_preview' => $this->getImagePreviewPlaceholder(),
            'image_mobile_preview' => $this->getImagePreviewPlaceholder(),
            'title' => [],
            'legend' => [],
            'url' => [],
            'description' => [],
            'active' => false,
            'display_from' => new \DateTime(),
            'display_to' => new \DateTime(),
            'shop_association' => $this->shopContext->getContextListShopID(),
        ];
    }
}
