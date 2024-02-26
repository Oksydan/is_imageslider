<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Form\Type;

use Oksydan\IsImageslider\Entity\ImageSlider;
use Oksydan\IsImageslider\Entity\ImageSliderLang;
use Oksydan\IsImageslider\Form\EventListener\ImagesliderFormSubscriber;
use Oksydan\IsImageslider\Form\Type\Lang\LangFieldsType;
use Oksydan\IsImageslider\Form\Type\Shop\ShopAssociationChoiceTreeType;
use Oksydan\IsImageslider\Form\Type\Slider\ImageSliderLangType;
use Oksydan\IsImageslider\Translations\TranslationDomains;
use PrestaShop\PrestaShop\Adapter\Feature\MultistoreFeature;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ImageSliderType extends TranslatorAwareType
{
    /**
     * @var MultistoreFeature
     */
    private MultistoreFeature $multistoreFeature;

    /**
     * @var ImagesliderFormSubscriber
     */
    private ImagesliderFormSubscriber $imagesliderFormSubscriber;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        MultistoreFeature $multistoreFeature,
        ImagesliderFormSubscriber $imagesliderFormSubscriber
    ) {
        parent::__construct($translator, $locales);

        $this->multistoreFeature = $multistoreFeature;
        $this->imagesliderFormSubscriber = $imagesliderFormSubscriber;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('images_for_all_langs', SwitchType::class, [
                'label' => $this->trans('Set one image for all languages', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'attr' => [
                    'class' => 'js-toggle-images-types',
                ],
            ])
            ->add('slider_langs', LangFieldsType::class, [
                'entry_type' => ImageSliderLangType::class,
                'entity_class' => ImageSliderLang::class,
                'entity_lang_field' => 'lang',
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

        if ($this->multistoreFeature->isUsed()) {
            $builder->add('shop_association', ShopAssociationChoiceTreeType::class, [
                'label' => $this->trans('Shop associations', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'You have to select at least one shop to associate this item with',
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ]);
        }

//        $builder->addEventSubscriber($this->imagesliderFormSubscriber);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ImageSlider::class,
        ]);
    }
}
