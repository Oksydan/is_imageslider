<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Form\Type\Shop;

use Oksydan\IsImageslider\Form\DataTransformer\Shop\ShopChoiceModelDataTransformer;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\ShopChoiceTreeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ShopChoiceTreeType.
 */
class ShopAssociationChoiceTreeType extends AbstractType
{
    private FormChoiceProviderInterface $shopTreeChoicesProvider;

    private ShopChoiceModelDataTransformer $shopChoiceModelDataTransformer;

    public function __construct(
        FormChoiceProviderInterface $shopTreeChoicesProvider,
        ShopChoiceModelDataTransformer $shopChoiceModelDataTransformer
    ) {
        $this->shopTreeChoicesProvider = $shopTreeChoicesProvider;
        $this->shopChoiceModelDataTransformer = $shopChoiceModelDataTransformer;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->shopChoiceModelDataTransformer);
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            [$this, 'onPreSetData']
        );

        parent::buildForm($builder, $options);
    }

    public function onPreSetData($event)
    {
        $data = $this->shopChoiceModelDataTransformer->transform($event->getData());

        $event->setData($data);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices_tree' => $this->shopTreeChoicesProvider->getChoices(),
            'multiple' => true,
            'choice_label' => 'name',
            'choice_value' => 'id_shop',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ShopChoiceTreeType::class;
    }
}
