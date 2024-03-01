<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Form\EventListener;

use PrestaShopBundle\Entity\Repository\ShopRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class ImagesliderFormSubscriber implements EventSubscriberInterface
{
    private ShopRepository $shopRepository;

    public function __construct(ShopRepository $shopRepository)
    {
        $this->shopRepository = $shopRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
//            FormEvents::PRE_SUBMIT => 'preSubmit',
            FormEvents::POST_SET_DATA => 'postSetData',
        ];
    }

    private function removeImageField(string $name, FormInterface $form)
    {
        if ($form->has($name)) {
            $form->remove($name);
        }
    }

    private function modifyFormBasedOnSelectedType(FormEvent $event)
    {
        $form = $event->getForm();
        $imagesForAllLangs = $form->get('images_for_all_langs')->getData() !== null ? $form->get('images_for_all_langs')->getData() : false;

        if ($imagesForAllLangs) {
            if ($form->has('slider_langs')) {
                $langsForm = $form->get('slider_langs');

                foreach ($langsForm->all() as $langForm) {
                    $this->removeImageField('image', $langForm);
                    $this->removeImageField('image_mobile', $langForm);
                }
            }
        } else {
            $this->removeImageField('image', $form);
            $this->removeImageField('image_mobile', $form);
        }
    }

    public function preSubmit(FormEvent $event): void
    {
        $this->modifyFormBasedOnSelectedType($event);
    }

    private function setDefaultData(FormEvent $event)
    {
        $imageSlider = $event->getData();
        $form = $event->getForm();

        // Set default values if the entity is empty (new form)
        if (null === $imageSlider || null === $imageSlider->getId()) {
            $form->get('display_from')->setData(new \DateTime());
            $form->get('display_to')->setData((new \DateTime())->modify('+1 month'));

            if ($form->has('shop_association')) {
                $shops = $this->shopRepository->findBy(['active' => true]);
                $shops = array_map(function ($shop) {
                    return $shop->getId();
                }, $shops);

                $form->get('shop_association')->setData($shops);
            }
        }
    }

    public function postSetData(FormEvent $event)
    {
        $this->setDefaultData($event);
        $this->modifyFormBasedOnSelectedType($event);
    }
}
