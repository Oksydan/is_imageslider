<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Repository;

use Doctrine\DBAL\Connection;

/**
 * Class HookModuleRepository is responsible for retrieving module data from database.
 */
class HookModuleRepository
{
    /**
     * @var Connection
     */
    private Connection $connection;

    /**
     * @var string
     */
    private string $dbPrefix;

    /**
     * @var string
     */
    private string $table;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     */
    public function __construct(Connection $connection, $dbPrefix)
    {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->table = $this->dbPrefix . 'hook_module';
    }

    public function getAllHookRegisteredToModule($moduleId)
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('h.name')
            ->from($this->table, 'mh')
            ->where('mh.id_module = :id_module')
            ->leftJoin('mh', $this->dbPrefix . 'hook', 'h', 'h.id_hook = mh.id_hook')
            ->setParameter('id_module', $moduleId);

        return $qb->execute()->fetchAll();
    }
}
