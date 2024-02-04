<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Falconize;

use Doctrine\DBAL\Connection;
use Oksydan\Falconize\FalconizeConfigurationInterface;
use Oksydan\Falconize\PrestaShop\Module\PrestaShopModuleInterface;

final class FalconizeConfiguration implements FalconizeConfigurationInterface
{
    protected PrestaShopModuleInterface $module;

    protected Connection $connection;

    protected string $databasePrefix;

    protected string $prestashopVersion;

    public function __construct(
        PrestaShopModuleInterface $module,
        Connection $connection,
        string $databasePrefix,
        string $prestashopVersion
    ) {
        $this->module = $module;
        $this->connection = $connection;
        $this->databasePrefix = $databasePrefix;
        $this->prestashopVersion = $prestashopVersion;
    }

    public function getConnection(): Connection
    {
        return $this->connection;
    }

    public function getModule(): PrestaShopModuleInterface
    {
        return $this->module;
    }

    public function getConfigurationFile(): \SplFileInfo
    {
        return new \SplFileInfo(__DIR__ . '/../../config/configuration.yml');
    }

    public function getDatabasePrefix(): string
    {
        return $this->databasePrefix;
    }

    public function getPrestashopVersion(): string
    {
        return $this->prestashopVersion;
    }
}
