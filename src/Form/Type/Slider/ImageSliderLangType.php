<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Form\Type\Slider;

use Oksydan\IsImageslider\Entity\ImageSliderLang;
use Oksydan\IsImageslider\Form\Type\Lang\LangType;
use Oksydan\IsImageslider\Translations\TranslationDomains;
use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class ImageSliderLangType extends AbstractType
{
    private TranslatorInterface $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(
        TranslatorInterface $translator
    ) {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lang', LangType::class)
            ->add('title', TextType::class, [
                'label' => $this->translator->trans('Title', [], TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'required' => true,
            ])
            ->add('legend', TextType::class, [
                'label' => $this->translator->trans('Legend', [], TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'required' => false,
            ])
            ->add('url', TextType::class, [
                'label' => $this->translator->trans('Link', [], TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('description', FormattedTextareaType::class, [
                'label' => $this->translator->trans('Description', [], TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'required' => false,
            ])
            ->add('image', ImageType::class, [
                'label' => $this->translator->trans('Image', [], TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'required' => false,
            ])
            ->add('image_mobile', ImageType::class, [
                'label' => $this->translator->trans('Image mobile', [], TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ImageSliderLang::class,
        ]);
    }
}
