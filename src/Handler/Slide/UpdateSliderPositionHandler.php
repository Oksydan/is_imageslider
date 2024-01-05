<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Handler\Slide;

use Oksydan\IsImageslider\Cache\TemplateCache;
use PrestaShop\PrestaShop\Core\Grid\Position\Exception\PositionDataException;
use PrestaShop\PrestaShop\Core\Grid\Position\Exception\PositionUpdateException;
use PrestaShop\PrestaShop\Core\Grid\Position\GridPositionUpdater;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionDefinition;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionUpdateFactory;

class UpdateSliderPositionHandler
{
    protected TemplateCache $templateCache;

    protected GridPositionUpdater $gridPositionUpdater;

    public function __construct(
        TemplateCache $templateCache,
        GridPositionUpdater $gridPositionUpdater
    ) {
        $this->templateCache = $templateCache;
        $this->gridPositionUpdater = $gridPositionUpdater;
    }

    /**
     * @throws PositionUpdateException
     * @throws PositionDataException
     */
    public function handle(array $positions): void
    {
        $positionDefinition = new PositionDefinition(
            'image_slider',
            'id_slide',
            'position'
        );

        $positionUpdateFactory = new PositionUpdateFactory(
            'positions',
            'rowId',
            'oldPosition',
            'newPosition',
            'idParent'
        );

        $positionsData = [
            'positions' => $positions,
        ];

        $positionUpdate = $positionUpdateFactory->buildPositionUpdate($positionsData, $positionDefinition);

        $this->gridPositionUpdater->update($positionUpdate);

        $this->templateCache->clearTemplateCache();
    }
}
