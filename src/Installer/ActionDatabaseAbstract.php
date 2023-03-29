<?php

namespace Oksydan\IsImageslider\Installer;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;

abstract class ActionDatabaseAbstract
{
    public const HOOK_LIST = [];

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var string
     */
    protected $dbPrefix;

    /**
     * @var array
     */
    protected $queries = [];

    /**
     * @var array
     */
    protected $tableData = [];

    public function __construct(Connection $connection, string $dbPrefix)
    {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
    }

    public function execute(): bool
    {
        $result = true;

        foreach ($this->getQueries() as $query) {
            $statement = $this->connection->executeQuery($query);

            if ($statement instanceof Statement && 0 !== (int) $statement->errorCode()) {
                $result &= false;
            }
        }

        return $result;
    }

    public function setData(array $data)
    {
        $this->tableData = $data;

        return $this;
    }

    public function getData(): array
    {
        return $this->tableData;
    }

    public function getQueries(): array
    {
        return $this->queries;
    }

    public function setQueries($queries)
    {
        $this->queries = $queries;

        return $this;
    }
}
