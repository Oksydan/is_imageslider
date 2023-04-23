<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Hook;

use Oksydan\IsImageslider\Configuration\SliderConfiguration;

abstract class AbstractDisplayHook extends AbstractHook
{
    protected $sliderConfiguration;

    public function __construct(
        \Module $module,
        \Context $context,
        SliderConfiguration $sliderConfiguration
    ) {
        parent::__construct($module, $context);

        $this->sliderConfiguration = $sliderConfiguration;
    }

    public function execute(array $params): string
    {
        if (!$this->shouldBlockBeDisplayed($params)) {
            return '';
        }

        $this->assignTemplateVariables($params);

        return $this->module->fetch($this->getTemplateFullPath());
    }

    protected function assignTemplateVariables(array $params)
    {
    }

    protected function shouldBlockBeDisplayed(array $params)
    {
        return true;
    }

    public function getTemplateFullPath(): string
    {
        return "module:{$this->module->name}/views/templates/hook/{$this->getTemplate()}";
    }

    abstract protected function getTemplate(): string;
}
