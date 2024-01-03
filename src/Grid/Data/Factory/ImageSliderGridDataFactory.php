<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Grid\Data\Factory;

use Oksydan\IsImageslider\Provider\ImageProviderInterface;
use PrestaShop\PrestaShop\Core\Grid\Data\Factory\GridDataFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

final class ImageSliderGridDataFactory implements GridDataFactoryInterface
{
    /**
     * @var GridDataFactoryInterface
     */
    private GridDataFactoryInterface $imageSliderDataFactory;

    /**
     * @var ImageProviderInterface
     */
    private ImageProviderInterface $imagesliderImageThumbProvider;

    /**
     * @param GridDataFactoryInterface $imageSliderDataFactory
     * @param ImageProviderInterface $imagesliderImageThumbProvider
     */
    public function __construct(
        GridDataFactoryInterface $imageSliderDataFactory,
        ImageProviderInterface $imagesliderImageThumbProvider
    ) {
        $this->imageSliderDataFactory = $imageSliderDataFactory;
        $this->imagesliderImageThumbProvider = $imagesliderImageThumbProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $languageData = $this->imageSliderDataFactory->getData($searchCriteria);

        $modifiedRecords = $this->applyModification(
            $languageData->getRecords()->all()
        );

        return new GridData(
            new RecordCollection($modifiedRecords),
            $languageData->getRecordsTotal(),
            $languageData->getQuery()
        );
    }

    /**
     * @param array $sliders
     *
     * @return array
     */
    private function applyModification(array $sliders)
    {
        foreach ($sliders as $i => $slider) {
            $sliders[$i]['image'] = $this->imagesliderImageThumbProvider->getPath($slider['image']);
        }

        return $sliders;
    }
}
