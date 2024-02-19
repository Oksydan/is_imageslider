<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Form\Type\Lang;

use Oksydan\IsImageslider\Form\DataTransformer\Lang\LangModeDataTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

final class LangType extends AbstractType
{
    private LangModeDataTransformer $langModeDataTransformer;

    public function __construct(LangModeDataTransformer $langModeDataTransformer)
    {
        $this->langModeDataTransformer = $langModeDataTransformer;
    }

    public function getParent(): string
    {
        return HiddenType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->langModeDataTransformer);
    }
}
