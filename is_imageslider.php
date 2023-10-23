<?php

declare(strict_types=1);

if (!defined('_PS_VERSION_')) {
    exit;
}

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

use Oksydan\IsImageslider\Hook\HookInterface;
use Oksydan\IsImageslider\Installer\ImageSliderInstaller;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class Is_imageslider extends Module implements WidgetInterface
{
    public $multistoreCompatibility = self::MULTISTORE_COMPATIBILITY_YES;

    public function __construct()
    {
        $this->name = 'is_imageslider';

        /*
         * SPECIAL THANKS TO WAYNET TEAM
         * YOU ARE HUGE INSPIRATION TO ME
         * https://www.waynet.pl/
         */
        $this->author = 'Igor Stępień';
        $this->version = '2.3.2';
        $this->need_instance = 0;

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = 'Home slider module';
        $this->description = 'Home slider module';
        $this->ps_versions_compliancy = ['min' => '8.0.0', 'max' => _PS_VERSION_];
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
            && $this->registerHook('displayHeader')
            && $this->registerHook('displayHome')
            && $this->getInstaller()->createTables();
    }

    /**
     * @return bool
     */
    public function uninstall(): bool
    {
        return $this->getInstaller()->dropTables() && parent::uninstall();
    }

    public function getContent(): void
    {
        \Tools::redirectAdmin(SymfonyContainer::getInstance()->get('router')->generate('is_imageslider_controller'));
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

    /**
     * @return ImageSliderInstaller
     */
    private function getInstaller(): ImageSliderInstaller
    {
        try {
            $installer = $this->getService('oksydan.is_imageslider.image_slider_installer');
        } catch (Error $error) {
            $installer = null;
        }

        if (null === $installer) {
            $installer = new Oksydan\IsImageslider\Installer\ImageSliderInstaller(
                $this->getService('doctrine.dbal.default_connection'),
                new Oksydan\IsImageslider\Installer\DatabaseYamlParser(
                    new Oksydan\IsImageslider\Installer\Provider\DatabaseYamlProvider($this)
                ),
                $this->context
            );
        }

        return $installer;
    }

    /** @param string $methodName */
    public function __call($methodName, array $arguments)
    {
        if (str_starts_with($methodName, 'hook')) {
            if ($hook = $this->getHookObject($methodName)) {
                return $hook->execute(...$arguments);
            }
        } elseif (method_exists($this, $methodName)) {
            return $this->{$methodName}(...$arguments);
        } else {
            return null;
        }
    }

    /**
     * @param string $methodName
     *
     * @return HookInterface|null
     */
    private function getHookObject($methodName)
    {
        $serviceName = sprintf(
            'oksydan.is_imageslider.hook.%s',
            \Tools::toUnderscoreCase(str_replace('hook', '', $methodName))
        );

        $hook = $this->getService($serviceName);

        return $hook instanceof HookInterface ? $hook : null;
    }

    public function renderWidget($hookName, array $configuration)
    {
        $widgetCapability = $this->get('oksydan.is_imageslider.hook.widget_capability');

        return $widgetCapability->renderWidget($configuration);
    }

    public function getWidgetVariables($hookName, array $configuration)
    {
        $widgetCapability = $this->get('oksydan.is_imageslider.hook.widget_capability');

        return $widgetCapability->getWidgetVariables($configuration);
    }
}
