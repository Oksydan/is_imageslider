<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Hook;

use Context;
use Module;
use Oksydan\IsImageslider\Configuration\SliderConfiguration;

abstract class AbstractDisplayHook extends AbstractHook
{
    protected $sliderConfiguration;

    public function __construct(
        Module $module,
        Context $context,
        SliderConfiguration $sliderConfiguration
    ) {
        parent::__construct($module, $context);

        $this->sliderConfiguration = $sliderConfiguration;
    }

    public function execute(array $params): string
    {
        $this->assignTemplateVariables($params);

        return $this->module->fetch("module:{$this->module->name}views/templates/hook/{$this->getTemplate()}");
    }

    protected function assignTemplateVariables(array $params)
    {
    }

    abstract protected function getTemplate(): string;
}
