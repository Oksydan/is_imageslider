<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Form\DataTransformer\Shop;

use PrestaShopBundle\Entity\Repository\ShopRepository;
use PrestaShopBundle\Entity\Shop;
use Symfony\Component\Form\DataTransformerInterface;

class ShopChoiceModelDataTransformer implements DataTransformerInterface
{
    private ShopRepository $shopRepository;

    public function __construct(ShopRepository $shopRepository)
    {
        $this->shopRepository = $shopRepository;
    }

    public function transform($value)
    {
        $shopList = [];

        if (null === $value) {
            return $shopList;
        }

        foreach ($value as $shop) {
            if ($shop instanceof Shop) {
                $shopList[] = $shop->getId();
            }
        }

        return $shopList;
    }

    public function reverseTransform($value): array
    {
        $shopList = [];

        if (!$value || !is_array($value)) {
            return $shopList;
        }

        foreach ($value as $shopId) {
            $shop = $this->shopRepository->findOneById((int) $shopId);

            if ($shop) {
                $shopList[] = $shop;
            }
        }

        return $shopList;
    }
}
