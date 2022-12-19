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
            ->orderBy('s.position', 'ASC')
            ->setMaxResults(1)
            ->getQuery();

        $result = $qb->getOneOrNullResult();

        if ($result) {
            $position = $result['position'];
        }

        return $position;
    }

    public function getActiveSliderByLandAndStoreId(
        int $idLang,
        int $idStore,
        bool $activeOnly = true,
        int $limit = 0
    ): array {
        $slides = [];

        $qb = $this
            ->createQueryBuilder('s')
            ->select('sl.title, sl.legend, sl.url, sl.description, sl.image, sl.imageMobile')
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

        $slides = $qb->getQuery()->getScalarResult();

        return $slides;
    }
}
