<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Form;

use Oksydan\IsImageslider\Translations\TranslationDomains;
use Oksydan\IsImageslider\Type\TranslatableFile;
use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;
use PrestaShopBundle\Form\Admin\Type\ImagePreviewType;
use PrestaShopBundle\Form\Admin\Type\ShopChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class ImageSliderType extends TranslatorAwareType
{
    /**
     * @var bool
     */
    private $isMultistoreUsed;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param bool $isMultistoreUsed
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        bool $isMultistoreUsed
    ) {
        parent::__construct($translator, $locales);

        $this->isMultistoreUsed = $isMultistoreUsed;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isEdit = !empty($options['data']['image']);

        $imageConstrains = [
            new File([
                'mimeTypes' => [
                    'image/jpeg',
                    'image/png',
                ],
            ]),
        ];

        if (!$isEdit) {
            $imageConstrains[] = new NotBlank();
        }

        $builder
            ->add('image_preview', TranslatableType::class, [
                'type' => ImagePreviewType::class,
                'label' => $this->trans('Image preview', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'locales' => $this->locales,
                'required' => false,
            ])
            ->add('image', TranslatableFile::class, [
                'type' => FileType::class,
                'label' => $this->trans('Image', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'locales' => $this->locales,
                'options' => [
                    'data_class' => null,
                    'constraints' => $imageConstrains,
                ],
                'required' => true,
            ])
            ->add('image_mobile_preview', TranslatableType::class, [
                'type' => ImagePreviewType::class,
                'label' => $this->trans('Image mobile preview', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'locales' => $this->locales,
                'required' => false,
            ])
            ->add('image_mobile', TranslatableFile::class, [
                'type' => FileType::class,
                'label' => $this->trans('Image mobile', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'locales' => $this->locales,
                'options' => [
                    'data_class' => null,
                    'constraints' => $imageConstrains,
                ],
                'required' => true,
            ])
            ->add('title', TranslatableType::class, [
                'type' => TextType::class,
                'label' => $this->trans('Title', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'locales' => $this->locales,
                'required' => false,
            ])
            ->add('legend', TranslatableType::class, [
                'type' => TextType::class,
                'label' => $this->trans('Legend', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'locales' => $this->locales,
                'required' => false,
            ])
            ->add('url', TranslatableType::class, [
                'type' => TextType::class,
                'label' => $this->trans('Link', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'locales' => $this->locales,
                'required' => true,
                'options' => [
                    'constraints' => [
                        new NotBlank(),
                    ],
                ],
            ])
            ->add('description', TranslatableType::class, [
                'type' => FormattedTextareaType::class,
                'locales' => $this->locales,
                'label' => $this->trans('Description', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'required' => false,
            ])
            ->add('active', SwitchType::class, [
                'label' => $this->trans('Active', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'required' => true,
            ])
            ->add('display_from', DateTimeType::class, [
                'label' => $this->trans('Display from', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'required' => true,
                'widget' => 'single_text',
                'html5' => true,
                'input' => 'datetime',
                'with_seconds' => true,
            ])
            ->add('display_to', DateTimeType::class, [
                'label' => $this->trans('Display to', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'required' => true,
                'widget' => 'single_text',
                'html5' => true,
                'input' => 'datetime',
                'with_seconds' => true,
            ]);

        if ($this->isMultistoreUsed) {
            $builder->add(
                'shop_association',
                ShopChoiceTreeType::class,
                [
                    'label' => $this->trans('Shop associations', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                    'constraints' => [
                        new NotBlank([
                            'message' => $this->trans(
                                'You have to select at least one shop to associate this item with',
                                'Admin.Notifications.Error'
                            ),
                        ]),
                    ],
                ]
            );
        }
    }
}
