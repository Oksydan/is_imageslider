<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Form;

use Oksydan\IsImageslider\Configuration\SliderConfiguration;
use Oksydan\IsImageslider\Translations\TranslationDomains;
use PrestaShopBundle\Form\Admin\Type\MultistoreConfigurationType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Range;

class ImageSliderConfigurationType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $minTime = 1000;
        $maxTime = 60000;
        $rangeInvalidMessage = $this->trans(
            'This field value have to be between %min%ms and %max%ms.',
            TranslationDomains::TRANSLATION_DOMAIN_ADMIN,
            [
                '%min%' => $minTime,
                '%max%' => $maxTime,
            ]
        );

        $builder
            ->add('speed', TextType::class, [
                'attr' => ['class' => 'col-md-4 col-lg-2'],
                'required' => true,
                'label' => $this->trans('Speed', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'help' => $this->trans('The duration of the transition between two slides.', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'multistore_configuration_key' => SliderConfiguration::HOMESLIDER_SPEED,
                'constraints' => [
                    new Range([
                        'min' => $minTime,
                        'max' => $maxTime,
                        'invalidMessage' => $rangeInvalidMessage,
                        'maxMessage' => $rangeInvalidMessage,
                        'minMessage' => $rangeInvalidMessage,
                    ]),
                ],
            ])
            ->add('pause', SwitchType::class, [
                'label' => $this->trans('Pause on hover', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'help' => $this->trans('Stop sliding when the mouse cursor is over the slideshow.', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'multistore_configuration_key' => SliderConfiguration::HOMESLIDER_PAUSE_ON_HOVER,
            ])
            ->add('wrap', SwitchType::class, [
                'label' => $this->trans('Wrap', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'help' => $this->trans('Loop or stop after the last slide.', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'multistore_configuration_key' => SliderConfiguration::HOMESLIDER_WRAP,
            ]);
    }

    /**
     * {@inheritdoc}
     *
     * @see MultistoreConfigurationTypeExtension
     */
    public function getParent(): string
    {
        return MultistoreConfigurationType::class;
    }
}
