<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Hook;

abstract class AbstractHook implements HookInterface
{
    protected \Is_imageslider $module;
    protected \Context $context;

    public function __construct(\Is_imageslider $module, \Context $context)
    {
        $this->module = $module;
        $this->context = $context;
    }
}
