<?php

namespace Oksydan\IsImageslider\Hook;

class WidgetCapability extends AbstractCacheableDisplayHook
{
    private const TEMPLATE_FILE = 'slider.tpl';

    protected function getTemplate(): string
    {
        return WidgetCapability::TEMPLATE_FILE;
    }

    protected function getCacheKey(): string
    {
        return parent::getCacheKey() . '_' . ($this->context->isMobile() ? 'mobile' : 'desktop');
    }

    /**
     * @return array
     */
    private function getSlides(): array
    {
        $now = new \DateTime();
        $slides = $this->slideRepository->getActiveSliderByLandAndStoreId(
            $this->context->language->id,
            $this->context->shop->id,
            true,
            0, // 0 means no limit
            $now
        );

        foreach ($slides as &$slide) {
            $slide = $this->slidePresenter->present($slide);
        }

        return $slides;
    }

    public function getWidgetVariables($params): array
    {
        return [
            'homeslider' => [
                'slides' => $this->getSlides(),
                'speed' => $this->sliderConfiguration->getSliderSpeed(),
                'pause' => $this->sliderConfiguration->getSliderPauseOnHover(),
                'wrap' => $this->sliderConfiguration->getSliderWrap(),
            ],
        ];
    }

    protected function assignTemplateVariables(array $params)
    {
        $this->context->smarty->assign($this->getWidgetVariables($params));
    }

    public function renderWidget($params): string
    {
        return $this->execute($params);
    }
}
