<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class ImagesliderFormSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SUBMIT => 'preSubmit',
            FormEvents::PRE_SET_DATA => 'preSetData',
        ];
    }

    private function modifyFormBasedOnSelectedType($form, $data)
    {
        $isEdit = $data['edit'] ?? false;
        $imagesForAllLangs = $data['images_for_all_langs'] ?? false;

        $imageConstrains = [
            new File([
                'mimeTypes' => [
                    'image/gif',
                    'image/jpeg',
                    'image/png',
                ],
            ]),
        ];

        if (!$isEdit) {
            $imageConstrains[] = new NotBlank();
        }

//        if ($imagesForAllLangs) {
//            $form->add('images', ImagesType::class, [
//                'imagesConstraints' => $imageConstrains,
//            ]);
//        } else {
//            $form->add('images', ImagesMultilangType::class, [
//                'imagesConstraints' => $imageConstrains,
//            ]);
//        }
    }

    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        $this->modifyFormBasedOnSelectedType($form, $data);
    }

    public function preSubmit(FormEvent $event): void
    {
        $data = $event->getData();
        $form = $event->getForm();

        $this->modifyFormBasedOnSelectedType($form, $data);
    }
}
