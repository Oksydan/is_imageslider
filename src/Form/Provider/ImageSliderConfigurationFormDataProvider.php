<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Form\Provider;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

class ImageSliderConfigurationFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var DataConfigurationInterface
     */
    private $imageSlideConfigurationDataConfiguration;

    /**
     * @param DataConfigurationInterface $imageSlideConfigurationDataConfiguration
     */
    public function __construct(DataConfigurationInterface $imageSlideConfigurationDataConfiguration)
    {
        $this->imageSlideConfigurationDataConfiguration = $imageSlideConfigurationDataConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(): array
    {
        return $this->imageSlideConfigurationDataConfiguration->getConfiguration();
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data): array
    {
        return $this->imageSlideConfigurationDataConfiguration->updateConfiguration($data);
    }
}
