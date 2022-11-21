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
    private $doctrineImageSliderDataFactory;

    /**
     * @var ImageProviderInterface
     */
    private $imagesliderImageThumbProvider;

    /**
     * @param GridDataFactoryInterface $doctrineImageSliderDataFactory
     * @param ImageProviderInterface $imagesliderImageThumbProvider
     */
    public function __construct(
        GridDataFactoryInterface $doctrineImageSliderDataFactory,
        ImageProviderInterface $imagesliderImageThumbProvider
    ) {
        $this->doctrineImageSliderDataFactory = $doctrineImageSliderDataFactory;
        $this->imagesliderImageThumbProvider = $imagesliderImageThumbProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $languageData = $this->doctrineImageSliderDataFactory->getData($searchCriteria);

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
