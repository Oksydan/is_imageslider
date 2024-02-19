<?php

declare(strict_types=1);

if (!defined('_PS_VERSION_')) {
    exit;
}

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
} else {
    throw new Exception('You must run "composer install --no-dev" command in module directory');
}

use Oksydan\Falconize\Falconize;
use Oksydan\Falconize\PrestaShop\Module\PrestaShopModuleInterface;
use Oksydan\IsImageslider\Falconize\FalconizeConfiguration;
use Oksydan\IsImageslider\Hook\HookInterface;
use Oksydan\IsImageslider\Hook\WidgetCapability;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class Is_imageslider extends Module implements WidgetInterface, PrestaShopModuleInterface
{
    public $multistoreCompatibility = self::MULTISTORE_COMPATIBILITY_YES;
    protected ?Falconize $falconize;

    public function __construct()
    {
        $this->name = 'is_imageslider';

        /*
         * SPECIAL THANKS TO WAYNET TEAM
         * YOU ARE HUGE INSPIRATION TO ME
         * https://www.waynet.pl/
         */
        $this->author = 'Igor Stępień';
        $this->version = '3.0.0';
        $this->need_instance = 0;

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = 'Home slider module';
        $this->description = 'Home slider module';
        $this->ps_versions_compliancy = ['min' => '8.0.0', 'max' => _PS_VERSION_];
    }

    public function getFalconize()
    {
        if (!isset($this->falconize)) {
            $falconizeConfiguration = new FalconizeConfiguration(
                $this,
                SymfonyContainer::getInstance()->get('doctrine.dbal.default_connection'),
                _DB_PREFIX_,
                _PS_VERSION_
            );
            $this->falconize = new Falconize($falconizeConfiguration);
        }

        return $this->falconize;
    }

    public function isUsingNewTranslationSystem(): bool
    {
        return true;
    }

    /**
     * @return bool
     */
    public function install(): bool
    {
        return
            parent::install()
            && $this->getFalconize()->install();
    }

    /**
     * @return bool
     */
    public function uninstall(): bool
    {
        return $this->getFalconize()->uninstall() && parent::uninstall();
    }

    public function getContent(): void
    {
        Tools::redirectAdmin(SymfonyContainer::getInstance()->get('router')->generate('admin_imageslider_controller_index'));
    }

    /**
     * @template T
     *
     * @param class-string<T>|string $serviceName
     *
     * @return T|object|null
     */
    public function getService($serviceName)
    {
        try {
            return $this->get($serviceName);
        } catch (ServiceNotFoundException $exception) {
            return null;
        }
    }

    /** @param string $methodName */
    public function __call($methodName, array $arguments)
    {
        if (str_starts_with($methodName, 'hook')) {
            if ($hook = $this->getHookObject($methodName)) {
                return $hook->execute(...$arguments);
            }
        } else {
            return null;
        }
    }

    public function getCacheId($name = null)
    {
        return parent::getCacheId($name);
    }

    public function _clearCache($template, $cache_id = null, $compile_id = null)
    {
        return parent::_clearCache($template, $cache_id, $compile_id);
    }

    /**
     * @param string $methodName
     *
     * @return HookInterface|null
     */
    private function getHookObject($methodName)
    {
        $serviceName = sprintf(
            'Oksydan\IsImageslider\Hook\%s',
            ucwords(str_replace('hook', '', $methodName))
        );

        $hook = $this->getService($serviceName);

        return $hook instanceof HookInterface ? $hook : null;
    }

    public function renderWidget($hookName, array $configuration)
    {
        $widgetCapability = $this->get(WidgetCapability::class);

        return $widgetCapability->renderWidget($configuration);
    }

    public function getWidgetVariables($hookName, array $configuration)
    {
        $widgetCapability = $this->get(WidgetCapability::class);

        return $widgetCapability->getWidgetVariables($configuration);
    }
}
