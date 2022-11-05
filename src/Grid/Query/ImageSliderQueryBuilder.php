<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

final class ImageSliderQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var Context
     */
    private $shopContext;

    private $contextLangId;

    /**
     * ImageSliderQueryBuilder constructor.
     *
     * @param Connection $connection
     * @param $dbPrefix
     * @param Context $shopContext
     */
    public function __construct(Connection $connection, $dbPrefix, Context $shopContext, $contextLangId)
    {
        parent::__construct($connection, $dbPrefix);

        $this->shopContext = $shopContext;
        $this->contextLangId = $contextLangId;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return QueryBuilder
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->getBaseQuery($searchCriteria->getFilters());
        $qb->select('islide.id_slide, islidel.title, islidel.description, islidel.image, islide.active, islide.position')
            ->join('islide', $this->dbPrefix . 'image_slider_lang', 'islidel', 'islidel.id_slide = islide.id_slide')
            ->where('islidel.id_lang = :langId')
            ->setParameter('langId', (int) $this->contextLangId);

        if (!$this->shopContext->isAllShopContext()) {
            $qb->join('islide', $this->dbPrefix . 'image_slider_shop', 'islides', 'islides.id_slide = islide.id_slide')
                ->where('islides.id_shop in (' . implode(', ', $this->shopContext->getContextListShopID()) . ')')
                ->groupBy('islide.id_slide');
        }

        $qb->orderBy(
            $searchCriteria->getOrderBy(),
            $searchCriteria->getOrderWay()
        )
            ->setFirstResult($searchCriteria->getOffset())
            ->setMaxResults($searchCriteria->getLimit());

        $qb->orderBy('position');

        return $qb;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return QueryBuilder
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->getBaseQuery();
        $qb->select('COUNT(DISTINCT islide.id_slide)');
        if (!$this->shopContext->isAllShopContext()) {
            $qb->join('islide', $this->dbPrefix . 'image_slider_shop', 'islides', 'islides.id_slide = islide.id_slide')
                ->where('islides.id_shop in (' . implode(', ', $this->shopContext->getContextListShopID()) . ')');
        }

        return $qb;
    }

    /**
     * @return QueryBuilder
     */
    private function getBaseQuery(): QueryBuilder
    {
        return $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'image_slider', 'islide');
    }
}
