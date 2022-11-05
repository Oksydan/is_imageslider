<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Cache;

use Context;
use Module;
use Tools;
use Oksydan\IsImageslider\Repository\HookModuleRepository;
use Oksydan\IsImageslider\Hook\AbstractCacheableDisplayHook;

class TemplateCache
{
    protected $module;
    protected $context;

    /**
     * @var HookModuleRepository
     */
    protected $hookModuleRepository;

    public function __construct(
      Module $module,
      Context $context,
      HookModuleRepository $hookModuleRepository
    )
    {
        $this->module = $module;
        $this->context = $context;
        $this->hookModuleRepository = $hookModuleRepository;
    }

    public function clearTemplateCache()
    {
        $hookedHooks = $this->hookModuleRepository->getAllHookRegisteredToModule($this->module->id);
        $uniqueHooks = [];

        foreach($hookedHooks as $hook) {
          if (!in_array($hook['name'], $uniqueHooks)) {
            $uniqueHooks[] = $hook['name'];
          }
        }

        foreach($uniqueHooks as $hook) {
          $this->clearCacheForHook($hook);
        }
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
        Tools::toUnderscoreCase(str_replace('hook', '', $hookName))
      );

      $hook = $this->module->getService($serviceName);

      return $hook instanceof AbstractCacheableDisplayHook ? $hook : null;
    }
}
