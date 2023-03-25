<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Hook;

class DisplayHeader extends AbstractCacheableDisplayHook
{
    private const TEMPLATE_FILE = 'head.tpl';

    protected function getTemplate(): string
    {
        return DisplayHeader::TEMPLATE_FILE;
    }

    protected function assignTemplateVariables(array $params)
    {
        $slide = $this->getSlide();

        $this->context->smarty->assign([
            'image' => $slide['image_url'] ?? null,
        ]);
    }

    /**
     * @return array
     */
    private function getSlide(): array
    {
        $now = new \DateTime();
        $slides = $this->slideRepository->getActiveSliderByLandAndStoreId(
            $this->context->language->id,
            $this->context->shop->id,
            true,
            1,
            $now
        );

        foreach ($slides as &$slide) {
            $slide = $this->slidePresenter->present($slide);
        }

        return count($slides) ? reset($slides) : [];
    }

    protected function getCacheKey(): string
    {
        return parent::getCacheKey() . '_' . ($this->context->isMobile() ? 'mobile' : 'desktop');
    }

    protected function shouldBlockBeDisplayed(array $params)
    {
        return $this->context->controller->getPageName() === 'index';
    }
}
