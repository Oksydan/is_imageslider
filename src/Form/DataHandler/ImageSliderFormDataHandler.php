<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Form\DataHandler;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Oksydan\IsImageslider\Entity\ImageSlider;
use Oksydan\IsImageslider\Entity\ImageSliderLang;
use Oksydan\IsImageslider\Handler\FileEraser;
use Oksydan\IsImageslider\Handler\FileUploader;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\FormDataHandlerInterface;
use PrestaShopBundle\Entity\Repository\LangRepository;
use PrestaShopBundle\Entity\Shop;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageSliderFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var EntityRepository
     */
    private $imageSliderRepository;

    /**
     * @var LangRepository
     */
    private $langRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var FileUploader
     */
    private $fileUploader;

    /**
     * @var FileEraser
     */
    private $fileEraser;

    /**
     * @var array
     */
    private $languages;

    public function __construct(
        EntityRepository $imageSliderRepository,
        LangRepository $langRepository,
        EntityManagerInterface $entityManager,
        FileUploader $fileUploader,
        FileEraser $fileEraser,
        array $languages
    ) {
        $this->imageSliderRepository = $imageSliderRepository;
        $this->langRepository = $langRepository;
        $this->entityManager = $entityManager;
        $this->fileUploader = $fileUploader;
        $this->fileEraser = $fileEraser;
        $this->languages = $languages;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data): int
    {
        $imageSlide = new ImageSlider();

        $imageSlide->setActive($data['active']);
        $imageSlide->setPosition($this->imageSliderRepository->getHighestPosition() + 1);
        $this->addAssociatedShops($imageSlide, $data['shop_association'] ?? null);

        foreach ($this->languages as $language) {
            $langId = (int) $language['id_lang'];
            $lang = $this->langRepository->findOneById($langId);
            $imageSliderLang = new ImageSliderLang();

            $imageSliderLang
                ->setLang($lang)
                ->setTitle($data['title'][$langId] ?? '')
                ->setUrl($data['url'][$langId] ?? '')
                ->setLegend($data['legend'][$langId] ?? '')
                ->setDescription($data['description'][$langId] ?? '');

            if (!empty($data['image'][$langId])) {
                $imageSliderLang->setImage($this->uploadFile($data['image'][$langId]));
            }

            if (!empty($data['image_mobile'][$langId])) {
                $imageSliderLang->setImageMobile($this->uploadFile($data['image_mobile'][$langId]));
            }

            $imageSlide->addImageSliderLang($imageSliderLang);
        }

        $imageSlide->setActive($data['active']);

        $this->entityManager->persist($imageSlide);
        $this->entityManager->flush();

        return $imageSlide->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data): int
    {
        $imageSlide = $this->entityManager->getRepository(ImageSlider::class)->find($id);

        $imageSlide->setActive($data['active']);
        $this->addAssociatedShops($imageSlide, $data['shop_association'] ?? null);

        foreach ($this->languages as $language) {
            $langId = (int) $language['id_lang'];
            $imageSliderLang = $imageSlide->getImageSliderLangByLangId($langId);

            if (null === $imageSliderLang) {
                continue;
            }

            $imageSliderLang
                ->setTitle($data['title'][$langId] ?? '')
                ->setUrl($data['url'][$langId] ?? '')
                ->setLegend($data['legend'][$langId] ?? '')
                ->setDescription($data['description'][$langId] ?? '');

            if (!empty($data['image'][$langId])) {
                $this->eraseFile($imageSliderLang->getImage());
                $imageSliderLang->setImage($this->uploadFile($data['image'][$langId]));
            }

            if (!empty($data['image_mobile'][$langId])) {
                $this->eraseFile($imageSliderLang->getImageMobile());
                $imageSliderLang->setImageMobile($this->uploadFile($data['image_mobile'][$langId]));
            }
        }

        $this->entityManager->flush();

        return $imageSlide->getId();
    }

    /**
     * @param ImageSlider $imageSlide
     * @param array|null $shopIdList
     */
    private function addAssociatedShops(ImageSlider &$imageSlide, array $shopIdList = null): void
    {
        $imageSlide->clearShops();

        if (empty($shopIdList)) {
            return;
        }

        foreach ($shopIdList as $shopId) {
            $shop = $this->entityManager->getRepository(Shop::class)->find($shopId);
            $imageSlide->addShop($shop);
        }
    }

    private function uploadFile(UploadedFile $file): string
    {
        return $this->fileUploader->upload($file);
    }

    private function eraseFile(string $fileName): bool
    {
        return $this->fileEraser->remove($fileName);
    }
}
