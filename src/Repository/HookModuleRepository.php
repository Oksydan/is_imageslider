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
    private $connection;

    /**
     * @var string
     */
    private $databasePrefix;

    /**
     * @var string
     */
    private $table;

    /**
     * @param Connection $connection
     * @param string $databasePrefix
     */
    public function __construct(Connection $connection, $databasePrefix)
    {
        $this->connection = $connection;
        $this->databasePrefix = $databasePrefix;
        $this->table = $this->databasePrefix . 'hook_module';
    }

    public function getAllHookRegisteredToModule($moduleId)
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('h.name')
            ->from($this->table, 'mh')
            ->where('mh.id_module = :id_module')
            ->leftJoin('mh', $this->databasePrefix . 'hook', 'h', 'h.id_hook = mh.id_hook')
            ->setParameter('id_module', $moduleId);

        return $qb->execute()->fetchAll();
    }
}
