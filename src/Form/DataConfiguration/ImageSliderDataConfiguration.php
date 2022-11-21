<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Form\DataConfiguration;

use Oksydan\IsImageslider\Configuration\SliderConfiguration;
use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Handles configuration data for demo multistore configuration options.
 */
final class ImageSliderDataConfiguration extends AbstractMultistoreConfiguration
{
    private const CONFIGURATION_FIELDS = [
        'speed',
        'pause',
        'wrap',
    ];

    /**
     * @return OptionsResolver
     */
    protected function buildResolver(): OptionsResolver
    {
        return (new OptionsResolver())
            ->setDefined(self::CONFIGURATION_FIELDS)
            ->setAllowedTypes('speed', 'string')
            ->setAllowedTypes('pause', 'bool')
            ->setAllowedTypes('wrap', 'bool');
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration(): array
    {
        $return = [];
        $shopConstraint = $this->getShopConstraint();

        $return['speed'] = $this->configuration->get(SliderConfiguration::HOMESLIDER_SPEED, null, $shopConstraint);
        $return['pause'] = $this->configuration->get(SliderConfiguration::HOMESLIDER_PAUSE_ON_HOVER, null, $shopConstraint);
        $return['wrap'] = $this->configuration->get(SliderConfiguration::HOMESLIDER_WRAP, null, $shopConstraint);

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration): array
    {
        $shopConstraint = $this->getShopConstraint();
        $this->updateConfigurationValue(SliderConfiguration::HOMESLIDER_SPEED, 'speed', $configuration, $shopConstraint);
        $this->updateConfigurationValue(SliderConfiguration::HOMESLIDER_PAUSE_ON_HOVER, 'pause', $configuration, $shopConstraint);
        $this->updateConfigurationValue(SliderConfiguration::HOMESLIDER_WRAP, 'wrap', $configuration, $shopConstraint);

        return [];
    }
}
