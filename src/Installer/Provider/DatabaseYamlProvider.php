<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Installer\Provider;

use Oksydan\IsImageslider\Exceptions\DatabaseYamlFileNotExistsException;

class DatabaseYamlProvider
{
    /**
     * @var \Is_imageslider
     */
    protected $module;

    public function __construct(\Is_imageslider $module)
    {
        $this->module = $module;
    }

    public function getDatabaseFilePath(): string
    {
        $filePossiblePath = _PS_MODULE_DIR_ . $this->module->name . '/config/';
        $databaseFileName = 'database.yml';
        $fullFilePath = $filePossiblePath . $databaseFileName;

        if (file_exists($fullFilePath)) {
            return $fullFilePath;
        } else {
            throw new DatabaseYamlFileNotExistsException($databaseFileName . ' file not eixtst in ' . $filePossiblePath);
        }
    }
}
