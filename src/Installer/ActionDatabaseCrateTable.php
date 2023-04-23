<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Installer;

class ActionDatabaseCrateTable extends ActionDatabaseAbstract implements ActionDatabaseInterface
{
    public const defaultEngine = 'InnoDb';
    public const defaultCharset = 'UTF8';

    public function buildQuery(): void
    {
        $tablesArray = $this->tableData['database'] ?? [];
        $this->setQueries([]);
        $queriesArray = [];

        foreach ($tablesArray as $tableName => $table) {
            $queriesArray[] = $this->buildSingleCreateQuery($tableName, $table);
        }

        $this->setQueries($queriesArray);
    }

    private function buildSingleCreateQuery($tableName, $table): string
    {
        $dbQuery = '';

        if (empty($table['columns']) || empty($tableName)) {
            return $dbQuery;
        }

        $dbQuery .= 'CREATE TABLE IF NOT EXISTS ' . $this->dbPrefix . $tableName;
        $dbQuery .= ' (';

        $dbColumnsQuery = [];

        foreach ($table['columns'] as $columnName => $column) {
            $dbColumnsQuery[] = $columnName . ' ' . $column;
        }

        if (!empty($table['primary'])) {
            $dbColumnsQuery[] = 'PRIMARY KEY (' . implode(', ', $table['primary']) . ')';
        }

        $dbQuery .= implode(',', $dbColumnsQuery);

        $dbQuery .= ')';

        $dbQuery .= ' ENGINE = ' . (!empty($table['engine']) ? $table['engine'] : ActionDatabaseCrateTable::defaultEngine);

        $dbQuery .= ' DEFAULT CHARACTER SET ' . (!empty($table['charset']) ? $table['charset'] : ActionDatabaseCrateTable::defaultCharset);

        return $dbQuery;
    }
}
