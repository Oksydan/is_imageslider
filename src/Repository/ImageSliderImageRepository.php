<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Oksydan\IsImageslider\Entity\ImageSliderImage;

class ImageSliderImageRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImageSliderImage::class);
    }
}
