<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Form\DataTransformer\Lang;

use PrestaShopBundle\Entity\Lang;
use PrestaShopBundle\Entity\Repository\LangRepository;
use Symfony\Component\Form\DataTransformerInterface;

class LangModeDataTransformer implements DataTransformerInterface
{
    private LangRepository $langRepository;

    public function __construct(LangRepository $langRepository)
    {
        $this->langRepository = $langRepository;
    }

    public function transform($value)
    {
        if ($value instanceof Lang) {
            return $value->getId();
        }

        return $value;
    }

    public function reverseTransform($value)
    {
        if (is_int((int) $value)) {
            return $this->langRepository->find($value);
        }

        return $value;
    }
}
