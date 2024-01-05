<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Form\Provider;

use Oksydan\IsImageslider\Form\DataConfiguration\ImageSliderDataConfiguration;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

class ImageSliderConfigurationFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var ImageSliderDataConfiguration
     */
    private ImageSliderDataConfiguration $imageSlideConfigurationDataConfiguration;

    /**
     * @param ImageSliderDataConfiguration $imageSlideConfigurationDataConfiguration
     */
    public function __construct(ImageSliderDataConfiguration $imageSlideConfigurationDataConfiguration)
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
