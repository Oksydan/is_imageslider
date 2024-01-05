<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Grid\Definition\Factory;

use Oksydan\IsImageslider\Translations\TranslationDomains;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ImageColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\PositionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ToggleColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AbstractGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ImageSliderGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    private TranslatorInterface $trans;

    public const GRID_ID = 'is_imageslider';

    /**
     * @param HookDispatcherInterface|null $hookDispatcher
     * @param TranslatorInterface $trans
     */
    public function __construct(
        HookDispatcherInterface $hookDispatcher = null,
        TranslatorInterface $trans
    ) {
        parent::__construct($hookDispatcher);
        $this->trans = $trans;
    }

    /**
     * {@inheritdoc}
     */
    protected function getId()
    {
        return self::GRID_ID;
    }

    /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return $this->trans->trans('Image slider', [], TranslationDomains::TRANSLATION_DOMAIN_ADMIN);
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add(
                (new PositionColumn('position'))
                    ->setName($this->trans->trans('Position', [], 'Admin.Global'))
                    ->setOptions([
                        'id_field' => 'id_slide',
                        'position_field' => 'position',
                        'update_route' => 'is_imageslider_controller_update_positions',
                        'update_method' => 'POST',
                    ])
            )
            ->add(
                (new ImageColumn('image'))
                    ->setName($this->trans->trans('Image', [], 'Admin.Global'))
                    ->setOptions([
                        'src_field' => 'image',
                    ])
            )
            ->add(
                (new DataColumn('title'))
                    ->setName($this->trans->trans('Title', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'title',
                    ])
            )
            ->add(
                (new DataColumn('id_slide'))
                    ->setName($this->trans->trans('ID', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'id_slide',
                    ])
            )
            ->add(
                (new DataColumn('title'))
                    ->setName($this->trans->trans('Title', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'title',
                    ])
            )
            ->add(
                (new ToggleColumn('active'))
                    ->setName($this->trans->trans('Displayed', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'active',
                        'primary_field' => 'id_slide',
                        'route' => 'is_imageslider_controller_toggle_status',
                        'route_param_name' => 'slideId',
                    ])
            )
            ->add(
                (new ActionColumn('actions'))
                    ->setOptions([
                        'actions' => (new RowActionCollection())
                            ->add(
                                (new LinkRowAction('edit'))
                                    ->setIcon('edit')
                                    ->setOptions([
                                        'route' => 'is_imageslider_controller_edit',
                                        'route_param_name' => 'slideId',
                                        'route_param_field' => 'id_slide',
                                    ])
                            )
                            ->add(
                                (new LinkRowAction('delete'))
                                    ->setName($this->trans->trans('Delete', [], 'Admin.Actions'))
                                    ->setIcon('delete')
                                    ->setOptions([
                                        'route' => 'is_imageslider_controller_delete',
                                        'route_param_name' => 'slideId',
                                        'route_param_field' => 'id_slide',
                                        'confirm_message' => $this->trans->trans(
                                            'Delete selected item?',
                                            [],
                                            'Admin.Notifications.Warning'
                                        ),
                                    ])
                            ),
                    ])
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilters()
    {
        return new FilterCollection();
    }

    /**
     * {@inheritdoc}
     */
    protected function getGridActions()
    {
        return new GridActionCollection();
    }

    /**
     * {@inheritdoc}
     */
    protected function getBulkActions()
    {
        return new BulkActionCollection();
    }
}
