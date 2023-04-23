<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Installer;

class ActionDatabaseModifyColumn extends ActionDatabaseAbstract implements ActionDatabaseInterface
{
    public function buildQuery(): void
    {
        $tablesArray = $this->tableData['database_modify'] ?? [];
        $this->setQueries([]);
        $queriesArray = [];

        foreach ($tablesArray as $tableName => $table) {
            if (!empty($table['columns'])) {
                foreach ($table['columns'] as $columnName => $columnDefinition) {
                    if ($this->checkColumnExistenceInTable($tableName, $columnName)) {
                        $queriesArray[] = $this->buildSingleModifyQuery($tableName, $columnName, $columnDefinition);
                    }
                }
            }
        }

        $this->setQueries($queriesArray);
    }

    private function buildSingleModifyQuery($tableName, $columnName, $columnDefinition): string
    {
        $dbQuery = 'ALTER TABLE ' . $this->dbPrefix . $tableName . '
                    MODIFY COLUMN ' . $columnName . ' ' . $columnDefinition;

        return $dbQuery;
    }

    private function checkColumnExistenceInTable($tableName, $columnName): bool
    {
        $dbQuery = "SELECT count(*) 
                    FROM information_schema.COLUMNS 
                    WHERE TABLE_SCHEMA=DATABASE() 
                    AND COLUMN_NAME='" . $columnName . "'
                    AND TABLE_NAME='" . $this->dbPrefix . $tableName . "'";

        $statement = $this->connection->executeQuery($dbQuery);

        return $statement->rowCount() > 0;
    }
}
