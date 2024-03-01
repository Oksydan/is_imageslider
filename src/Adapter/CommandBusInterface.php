<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Adapter;

interface CommandBusInterface
{
    /**
     * Handle command.
     *
     * @param object $command
     */
    public function handle($command);
}
