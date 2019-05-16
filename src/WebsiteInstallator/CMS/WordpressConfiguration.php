<?php

/*
 * Vertiso (https://vertiso.pl)
 *
 * @copyright: Copyright (c) 2018 Vertiso (https://vertiso.pl)
 * @author Marcin Zagórski <vertiso851@gmail.com>
 */

namespace Vertiso\WebsiteInstallator\CMS;

use Vertiso\WebsiteInstallator\Exception\WebsiteInstallatorException;

class WordpressConfiguration
{
    private $dbHost;
    private $dbUsername;
    private $dbPassword;
    private $dbName;

    private $config = [];
    private $files = [];

    /**
     * WordpressConfiguration constructor.
     * @param $dbHost
     * @param $dbUsername
     * @param $dbPassword
     * @param $dbName
     * @throws WebsiteInstallatorException
     */
    public function __construct($dbHost, $dbUsername, $dbPassword, $dbName)
    {
        $this->config = [
            'sampleConfigFiles' => [
                'wp-config.php' => __DIR__ . '/../Resources/sample/wp-config.sample',
            ],
        ];

        $this->dbHost = $dbHost;
        $this->dbUsername = $dbUsername;
        $this->dbPassword = $dbPassword;
        $this->dbName = $dbName;

        $this->prepareConfigFiles();
    }

    /**
     * @return array
     * @throws WebsiteInstallatorException
     */
    private function toOverrides()
    {
        return [
            'wp-config.php' => [
                '{DB_HOST}' => $this->dbHost,
                '{DB_USER}' => $this->dbUsername,
                '{DB_PASSWORD}' => $this->dbPassword,
                '{DB_NAME}' => $this->dbName,
                '{SECRET_KEYS}' => $this->generateSecretKeys(),
            ],
        ];
    }

    /**
     * @return bool|string
     * @throws WebsiteInstallatorException
     */
    private function generateSecretKeys()
    {
        $secretKeys = @file_get_contents('https://api.wordpress.org/secret-key/1.1/salt/');

        if (!$secretKeys || empty($secretKeys)) {
            throw new WebsiteInstallatorException('WordpressConfiguration: Secret keys generate failed!');
        }

        return $secretKeys;
    }

    /**
     * @return array
     */
    public function getConfigurationFilesContents()
    {
        return $this->files;
    }

    /**
     * @throws WebsiteInstallatorException
     */
    private function prepareConfigFiles()
    {
        $toOverrides = $this->toOverrides();

        if (count($toOverrides)) {
            foreach ($toOverrides as $fileName => $configs) {
                $fileContent = file_get_contents($this->config['sampleConfigFiles'][$fileName]);
                $this->files[$fileName] = str_replace(array_keys($configs), array_values($configs), $fileContent);
            }
        }
    }
}
