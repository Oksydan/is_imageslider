<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Adapter;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface as CoreCommandBusInterface;

final class CommandBus implements CommandBusInterface
{
    private CoreCommandBusInterface $commandBus;

    public function __construct(CoreCommandBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function handle($command)
    {
        $this->commandBus->handle($command);
    }
}

