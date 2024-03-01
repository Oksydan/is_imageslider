<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Form\Type\Lang;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShopBundle\Entity\Repository\LangRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class LangFieldsType extends AbstractType
{
    private LangRepository $langRepository;

    private Configuration $configuration;

    private LegacyContext $legacyContext;

    public function __construct(
        LangRepository $langRepository,
        Configuration $configuration,
        LegacyContext $legacyContext
    ) {
        $this->langRepository = $langRepository;
        $this->configuration = $configuration;
        $this->legacyContext = $legacyContext;
    }

    public function getParent(): string
    {
        return CollectionType::class;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $locales = $this->langRepository->findAll();

        $view->vars['locales'] = $locales;
        $view->vars['default_locale'] = $this->getDefaultLocale($locales);
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        foreach ($view->children as $child) {
            $langGetter = 'get' . ucfirst($options['entity_lang_field']);
            $langAwareEntity = $child->vars['value'];

            if (!method_exists($langAwareEntity, $langGetter)) {
                throw new \InvalidArgumentException(sprintf('Method %s does not exist in class %s', $langGetter, $options['entity_class']));
            }

            $child->vars['form_locale'] = $child->vars['value']->{$langGetter}();
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'allow_delete' => false,
            'allow_add' => false,
            'entity_lang_field' => 'lang',
        ]);

        $resolver->setRequired([
            'entity_class',
        ]);

        $resolver->setAllowedTypes('entity_class', 'string');
        $resolver->setAllowedTypes('entity_lang_field', 'string');
    }

    private function fillDefaultData(array $options): array
    {
        $emptyData = [];

        foreach ($this->langRepository->findAll() as $lang) {
            $langSetter = 'set' . ucfirst($options['entity_lang_field']);

            $langAwareEntity = new $options['entity_class']();
            $langAwareEntity->setLang($lang);

            if (!method_exists($langAwareEntity, $langSetter)) {
                throw new \InvalidArgumentException(sprintf('Method %s does not exist in class %s', $langSetter, $options['entity_class']));
            }

            $langAwareEntity->{$langSetter}($lang);

            $emptyData[] = $langAwareEntity;
        }

        return $emptyData;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setData($this->fillDefaultData($options));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'langfields';
    }

    /**
     * Get default locale.
     *
     * @param array $locales
     *
     * @return array
     */
    private function getDefaultLocale(array $locales)
    {
        $defaultShopLocaleId = $this->configuration->getInt('PS_LANG_DEFAULT', null);
        $defaultFormLocaledId = $this->legacyContext->getContext()->employee->id_lang ?? null;

        if ($defaultFormLocaledId) {
            foreach ($locales as $locale) {
                if ($locale->getId() == $defaultFormLocaledId) {
                    return $locale;
                }
            }
        }

        foreach ($locales as $locale) {
            if ($locale->getId() === $defaultShopLocaleId) {
                return $locale;
            }
        }

        return reset($locales);
    }
}
