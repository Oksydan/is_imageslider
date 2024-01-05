<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Form\DataHandler;

use Doctrine\ORM\EntityManagerInterface;
use Oksydan\IsImageslider\Entity\ImageSlider;
use Oksydan\IsImageslider\Entity\ImageSliderLang;
use Oksydan\IsImageslider\Exceptions\DateRangeNotValidException;
use Oksydan\IsImageslider\Handler\FileEraser;
use Oksydan\IsImageslider\Handler\FileUploader;
use Oksydan\IsImageslider\Repository\ImageSliderRepository;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\FormDataHandlerInterface;
use PrestaShopBundle\Entity\Repository\LangRepository;
use PrestaShopBundle\Entity\Shop;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageSliderFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var ImageSliderRepository
     */
    private ImageSliderRepository $imageSliderRepository;

    /**
     * @var LangRepository
     */
    private LangRepository $langRepository;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var FileUploader
     */
    private FileUploader $fileUploader;

    /**
     * @var FileEraser
     */
    private FileEraser $fileEraser;

    /**
     * @var array
     */
    private array $locales;

    /**
     * @param ImageSliderRepository $imageSliderRepository
     * @param LangRepository $langRepository
     * @param EntityManagerInterface $entityManager
     * @param FileUploader $fileUploader
     * @param FileEraser $fileEraser
     * @param array $locales
     */
    public function __construct(
        ImageSliderRepository $imageSliderRepository,
        LangRepository $langRepository,
        EntityManagerInterface $entityManager,
        FileUploader $fileUploader,
        FileEraser $fileEraser,
        array $locales
    ) {
        $this->imageSliderRepository = $imageSliderRepository;
        $this->langRepository = $langRepository;
        $this->entityManager = $entityManager;
        $this->fileUploader = $fileUploader;
        $this->fileEraser = $fileEraser;
        $this->locales = $locales;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data): int
    {
        $this->assertSliderDataRangeActivity($data);

        $imageSlide = new ImageSlider();

        $imageSlide->setActive($data['active']);
        $imageSlide->setDisplayFrom($data['display_from'] ?? new \DateTime());
        $imageSlide->setDisplayTo($data['display_to'] ?? new \DateTime());
        $imageSlide->setPosition($this->imageSliderRepository->getHighestPosition() + 1);
        $this->addAssociatedShops($imageSlide, $data['shop_association'] ?? null);

        foreach ($this->locales as $language) {
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

        $this->entityManager->persist($imageSlide);
        $this->entityManager->flush();

        return $imageSlide->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data): int
    {
        $this->assertSliderDataRangeActivity($data);

        $imageSlide = $this->entityManager->getRepository(ImageSlider::class)->find($id);

        $imageSlide->setActive($data['active']);
        $imageSlide->setDisplayFrom($data['display_from'] ?? new \DateTime());
        $imageSlide->setDisplayTo($data['display_to'] ?? new \DateTime());
        $this->addAssociatedShops($imageSlide, $data['shop_association'] ?? null);

        foreach ($this->locales as $language) {
            $langId = (int) $language['id_lang'];
            $imageSliderLang = $imageSlide->getImageSliderLangByLangId($langId);

            $newImageSliderLang = false;
            if (null === $imageSliderLang) {
                $imageSliderLang = new ImageSliderLang();
                $lang = $this->langRepository->findOneById($langId);
                $imageSliderLang->setLang($lang);
                $newImageSliderLang = true;
            }

            $imageSliderLang
                ->setTitle($data['title'][$langId] ?? '')
                ->setUrl($data['url'][$langId] ?? '')
                ->setLegend($data['legend'][$langId] ?? '')
                ->setDescription($data['description'][$langId] ?? '');

            if (!empty($data['image'][$langId])) {
                if ($imageSliderLang->getImage() !== null) {
                    $this->eraseFile($imageSliderLang->getImage());
                }
                $imageSliderLang->setImage($this->uploadFile($data['image'][$langId]));
            }

            if (!empty($data['image_mobile'][$langId])) {
                if ($imageSliderLang->getImage() !== null) {
                    $this->eraseFile($imageSliderLang->getImageMobile());
                }
                $imageSliderLang->setImageMobile($this->uploadFile($data['image_mobile'][$langId]));
            }

            if ($newImageSliderLang) {
                $imageSlide->addImageSliderLang($imageSliderLang);
            }
        }

        $this->entityManager->flush();

        return $imageSlide->getId();
    }

    /**
     * @params array $data
     *
     * @return void
     *
     * @throws DateRangeNotValidException
     */
    private function assertSliderDataRangeActivity($data): void
    {
        if (!empty($data['display_from']) && !empty($data['display_to'])) {
            if ($data['display_from'] > $data['display_to']) {
                throw new DateRangeNotValidException();
            }
        }
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
