<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Installer;

use Symfony\Component\Yaml\Yaml;

class DatabaseYamlParser
{
    /**
     * @var DatabaseYamlProvider
     */
    protected $yamlProvider;

    /**
     * @var array
     */
    private $parsedFileData = [];

    public function __construct($yamlProvider)
    {
        $this->yamlProvider = $yamlProvider;
    }

    public function getDatabaseYmlFilePath(): string
    {
        return $this->yamlProvider->getDatabaseFilePath();
    }

    private function parseFile(): void
    {
        $this->parsedFileData = Yaml::parseFile($this->getDatabaseYmlFilePath());
    }

    public function getParsedFileData(): array
    {
        if (empty($this->parsedFileData)) {
            $this->parseFile();
        }

        return $this->parsedFileData;
    }
}
