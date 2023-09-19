<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class ImageSliderRepository extends EntityRepository
{
    public function getAllIds(): array
    {
        /** @var QueryBuilder $qb */
        $qb = $this
            ->createQueryBuilder('s')
            ->select('s.id')
        ;

        $slides = $qb->getQuery()->getScalarResult();

        return array_map(function ($slide) {
            return $slide['id'];
        }, $slides);
    }

    public function getHighestPosition(): int
    {
        $position = 0;
        $qb = $this
            ->createQueryBuilder('s')
            ->select('s.position')
            ->orderBy('s.position', 'DESC')
            ->setMaxResults(1)
            ->getQuery();

        $result = $qb->getOneOrNullResult();

        if ($result) {
            $position = $result['position'];
        }

        return $position;
    }

    private function addDateRangeFilter(QueryBuilder $qb, \DateTime $date): QueryBuilder
    {
        $qb
            ->andWhere('s.display_from <= :from')
            ->andWhere('s.display_to >= :to')
            ->setParameter('from', $date->format('Y-m-d H:i:s'))
            ->setParameter('to', $date->format('Y-m-d H:i:s'))
        ;

        return $qb;
    }

    public function getSimpleActiveSliderByStoreId(
        int $idStore,
        bool $activeOnly = true,
        int $limit = 0,
        \DateTime $date = null
    ): array {
        $qb = $this
            ->createQueryBuilder('s')
            ->select('s.id, s.position, s.active, s.display_from, s.display_to')
            ->join('s.shops', 'ss')
            ->andWhere('ss.id = :storeId')
            ->orderBy('s.position')
            ->setParameter('storeId', (int) $idStore);

        if ($activeOnly) {
            $qb->andWhere('s.active = 1');
        }

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        if ($date) {
            $qb = $this->addDateRangeFilter($qb, $date);
        }

        return $qb->getQuery()->getScalarResult();
    }

    public function getActiveSliderByLandAndStoreId(
        int $idLang,
        int $idStore,
        bool $activeOnly = true,
        int $limit = 0,
        \DateTime $date = null
    ): array {
        $qb = $this
            ->createQueryBuilder('s')
            ->select('sl.title, sl.legend, sl.url, sl.description, sl.image, sl.imageMobile, s.display_from, s.display_to')
            ->join('s.sliderLangs', 'sl')
            ->join('s.shops', 'ss')
            ->andWhere('sl.lang = :langId')
            ->andWhere('ss.id = :storeId')
            ->orderBy('s.position')
            ->setParameter('langId', (int) $idLang)
            ->setParameter('storeId', (int) $idStore);

        if ($activeOnly) {
            $qb->andWhere('s.active = 1');
        }

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        if ($date) {
            $qb = $this->addDateRangeFilter($qb, $date);
        }

        return $qb->getQuery()->getScalarResult();
    }
}
