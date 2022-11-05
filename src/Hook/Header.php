<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Hook;

class Header extends AbstractHook
{
    public const HOOK_LIST = [
        'displayHeader',
    ];

    /**
     * @param array $params
     *
     * @return string
     */
    public function hookDisplayHeader($params): string
    {
        $pageName = $this->context->controller->getPageName();
        $cacheKey = $this->module->getCacheId() . '_' . ($this->context->isMobile() ? 'mobile' : 'desktop');
        $template = "module:{$this->module->name}/views/templates/hook/head.tpl";

        if ($pageName !== 'index') {
            return '';
        }

        if (!$this->module->isCached($template, $cacheKey)) {
            $slide = $this->getSlide();

            if (empty($slide)) {
                return '';
            }

            $this->context->smarty->assign([
                'image' => $slide['image_url'],
            ]);
        }

        return $this->module->fetch($template, $cacheKey);
    }

    /**
     * @return array
     */
    private function getSlide(): array
    {
        $respository = $this->module->get('oksydan.is_imageslider.repository.image_slider');
        $slides = $respository->getActiveSliderByLandAndStoreId($this->context->language->id, $this->context->shop->id, true, 1);
        $presenter = $this->module->get('oksydan.is_imageslider.presenter.image_slide_presenter');

        foreach ($slides as &$slide) {
            $slide = $presenter->present($slide);
        }

        return count($slides) ? reset($slides) : [];
    }
}
