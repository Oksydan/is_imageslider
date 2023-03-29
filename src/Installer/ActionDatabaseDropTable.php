<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Installer;

use Oksydan\IsImageslider\Installer\ActionDatabaseAbstract;
use Oksydan\IsImageslider\Installer\ActionDatabaseInterface;

class ActionDatabaseDropTable extends ActionDatabaseAbstract implements ActionDatabaseInterface
{
    public function buildQuery(): void
    {
        $tablesArray = $this->tableData['database'] ?? [];
        $this->setQueries([]);
        $queriesArray = [];

        foreach ($tablesArray as $tableName => $table) {
            $queriesArray[] = $this->buildSingleDropQuery($tableName, $table);
        }

        $this->setQueries($queriesArray);
    }

    private function buildSingleDropQuery($tableName): string
    {
        $dbQuery = '';

        if (empty($tableName)) {
            return $dbQuery;
        }

        $dbQuery .= 'DROP TABLE IF EXISTS  ' . $this->dbPrefix . $tableName;

        return $dbQuery;
    }
}
