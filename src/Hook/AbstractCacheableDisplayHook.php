<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Hook;

use Context;
use Module;
use Oksydan\IsImageslider\Configuration\SliderConfiguration;
use Oksydan\IsImageslider\Presenter\ImageSlidePresenter;
use Oksydan\IsImageslider\Repository\ImageSliderRepository;

abstract class AbstractCacheableDisplayHook extends AbstractDisplayHook
{
    /**
     * @var ImageSliderRepository
     */
    protected $slideRepository;

    /**
     * @var ImageSlidePresenter
     */
    protected $slidePresenter;

    public function __construct(
        Module $module,
        Context $context,
        SliderConfiguration $sliderConfiguration,
        ImageSliderRepository $slideRepository,
        ImageSlidePresenter $slidePresenter
    ) {
        parent::__construct($module, $context, $sliderConfiguration);

        $this->slideRepository = $slideRepository;
        $this->slidePresenter = $slidePresenter;
    }

    public function execute(array $params): string
    {
        if (!$this->shouldBlockBeDisplayed($params)) {
            return '';
        }

        if (!$this->isTemplateCached()) {
            $this->assignTemplateVariables($params);
        }

        return $this->module->fetch($this->getTemplateFullPath(), $this->getCacheKey());
    }

    protected function getCacheKey(): string
    {
        return $this->module->getCacheId();
    }

    protected function isTemplateCached(): bool
    {
        return $this->module->isCached($this->getTemplateFullPath(), $this->getCacheKey());
    }

    public function getTemplateFullPath(): string
    {
        return "module:{$this->module->name}/views/templates/hook/{$this->getTemplate()}";
    }
}
