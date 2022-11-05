<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Hook;

use Is_imageslider;

class HookDispatcher
{
    public const HOOK_CLASSES = [
        Home::class,
        Header::class,
    ];

    /**
     * Hook instances.
     *
     * @var AbstractHook[]
     */
    protected $hooks = [];

    public function __construct(Is_imageslider $module)
    {
        foreach (static::HOOK_CLASSES as $hookClass) {
            /** @var AbstractHook $hook */
            $hook = new $hookClass($module);
            $this->hooks[] = $hook;
        }
    }

    /**
     * Get available hooks
     *
     * @return string[]
     */
    public function getAvailableHooks()
    {
        $availableHooks = [];
        foreach ($this->hooks as $hook) {
            $availableHooks = array_merge($availableHooks, $hook->getAvailableHooks());
        }

        return $availableHooks;
    }

    public function dispatch($hookName, array $params = [])
    {
        foreach ($this->hooks as $hook) {
            if (method_exists($hook, $hookName)) {
                return $hook->{$hookName}($params);
            }
        }

        return false;
    }
}
