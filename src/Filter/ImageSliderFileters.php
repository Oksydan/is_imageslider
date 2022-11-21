<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Filter;

use PrestaShop\PrestaShop\Core\Search\Filters;

/**
 * Class ProductFilter proves default filters for our products grid
 */
final class ImageSliderFileters extends Filters
{
    /**
     * {@inheritdoc}
     */
    public static function getDefaults(): array
    {
        return [
            'limit' => 10,
            'offset' => 0,
            'orderBy' => 'position',
            'sortOrder' => 'ASC',
            'filters' => [],
        ];
    }
}
