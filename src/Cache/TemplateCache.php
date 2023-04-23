<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Cache;

use Oksydan\IsImageslider\Hook\AbstractCacheableDisplayHook;
use Oksydan\IsImageslider\Repository\HookModuleRepository;
use Oksydan\IsImageslider\Repository\ImageSliderRepository;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

class TemplateCache
{
    protected $module;
    protected $context;

    /**
     * @var HookModuleRepository
     */
    protected $hookModuleRepository;

    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @var ImageSliderRepository
     */
    protected $slideRepository;

    public const IS_SLIDER_DATE_CACHE_KEY = 'IS_SLIDER_DATE_CACHE_KEY';

    private const DATE_TIME_FORMAT = 'Y-m-d H:i:s';

    public function __construct(
        \Module $module,
        \Context $context,
        HookModuleRepository $hookModuleRepository,
        Configuration $configuration,
        ImageSliderRepository $slideRepository
    ) {
        $this->module = $module;
        $this->context = $context;
        $this->hookModuleRepository = $hookModuleRepository;
        $this->configuration = $configuration;
        $this->slideRepository = $slideRepository;
    }

    public function clearTemplateCache()
    {
        $hookedHooks = $this->hookModuleRepository->getAllHookRegisteredToModule($this->module->id);
        $uniqueHooks = [];

        foreach ($hookedHooks as $hook) {
            if (!in_array($hook['name'], $uniqueHooks)) {
                $uniqueHooks[] = $hook['name'];
            }
        }

        foreach ($uniqueHooks as $hook) {
            $this->clearCacheForHook($hook);
        }

        $this->setCacheValidityDateForSlider();
    }

    private function clearCacheForHook($hookName)
    {
        $displayHook = $this->getServiceFromHookName($hookName);

        if ($displayHook) {
            $this->module->_clearCache($displayHook->getTemplateFullPath());
        }
    }

    private function getServiceFromHookName($hookName)
    {
        $serviceName = sprintf(
            'oksydan.is_imageslider.hook.%s',
            \Tools::toUnderscoreCase(str_replace('hook', '', $hookName))
        );

        $hook = $this->module->getService($serviceName);

        return $hook instanceof AbstractCacheableDisplayHook ? $hook : null;
    }

    private function setCacheValidityDate(\DateTime $date, ShopConstraint $shopConstraint): void
    {
        $this->configuration->set(self::IS_SLIDER_DATE_CACHE_KEY, $date->format(self::DATE_TIME_FORMAT), $shopConstraint);
    }

    private function getCacheValidityDate(ShopConstraint $shopConstraint): string
    {
        return $this->configuration->get(self::IS_SLIDER_DATE_CACHE_KEY, '', $shopConstraint);
    }

    private function resetCacheValidityDate(ShopConstraint $shopConstraint): void
    {
        $this->configuration->set(self::IS_SLIDER_DATE_CACHE_KEY, '', $shopConstraint);
    }

    public function clearTemplateCacheIfNeeded(int $idShop): void
    {
        $now = new \DateTime();
        $shopConstraint = ShopConstraint::shop($idShop);
        $date = $this->getCacheValidityDate($shopConstraint);
        $dateCacheKey = $date ? \DateTime::createFromFormat(self::DATE_TIME_FORMAT, $date) : null;

        if ($dateCacheKey && $now > $dateCacheKey) {
            $this->clearTemplateCache();
        }
    }

    public function setCacheValidityDateForSlider(): void
    {
        $stores = \Shop::getShops();

        foreach ($stores as $store) {
            $shopConstraint = ShopConstraint::shop((int) $store['id_shop']);

            $slides = $this->slideRepository->getSimpleActiveSliderByStoreId(
                $shopConstraint->getShopId()->getValue()
            );

            $this->setCacheValidityDateFromSliders($slides, $shopConstraint);
        }
    }

    private function setCacheValidityDateFromSliders(array $slides, ShopConstraint $shopConstraint): void
    {
        $closestDate = null;
        $now = new \DateTime();

        foreach ($slides as $slide) {
            $dateFrom = $slide['display_from'] ? \DateTime::createFromFormat(self::DATE_TIME_FORMAT, $slide['display_from']) : null;
            $dateTo = $slide['display_to'] ? \DateTime::createFromFormat(self::DATE_TIME_FORMAT, $slide['display_to']) : null;

            if ($dateFrom > $now && (($dateFrom && $closestDate && $closestDate > $dateFrom) || !$closestDate)) {
                $closestDate = $dateFrom;
            }

            if ($dateTo > $now && (($dateTo && $closestDate && $closestDate > $dateTo) || !$closestDate)) {
                $closestDate = $dateTo;
            }
        }

        if ($closestDate) {
            $this->setCacheValidityDate($closestDate, $shopConstraint);
        } else {
            $this->resetCacheValidityDate($shopConstraint);
        }
    }
}
