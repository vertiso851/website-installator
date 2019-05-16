<?php

/*
 * Vertiso (https://vertiso.pl)
 *
 * @copyright: Copyright (c) 2018 Vertiso (https://vertiso.pl)
 * @author Marcin Zagórski <vertiso851@gmail.com>
 */

namespace Vertiso\WebsiteInstallator;

use Vertiso\WebsiteInstallator\Client\Client;

class InstallationManager
{
    private $client;
    private $sshPathToRoot;
    private $ftpPathToRoot;

    /**
     * InstallationManager constructor.
     * @param Client $client
     * @param array $path
     */
    public function __construct(Client $client, array $path)
    {
        $this->client = $client;
        $this->sshPathToRoot = $path['ssh'];
        $this->ftpPathToRoot = $path['ftp'];
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return string
     */
    public function getSshPathToRoot()
    {
        return $this->sshPathToRoot;
    }

    public function getFtpPathToRoot()
    {
        return $this->ftpPathToRoot;
    }

    /**
     * @throws Exception\WebsiteInstallatorException
     */
    public function removeAllFilesFromRootDirectory()
    {
        $this->client->getSSH()->remove($this->getSshPathToRoot() . '/*');
    }

    /**
     * @param $sourceUrl
     * @param $archiveFileName
     * @throws Exception\WebsiteInstallatorException
     */
    public function pushProjectFilesToServer($sourceUrl, $archiveFileName)
    {
        $this->client->getSSH()->download($sourceUrl . '/' . $archiveFileName, $this->getSshPathToRoot());
    }

    /**
     * @param $archiveFileName
     * @throws Exception\WebsiteInstallatorException
     */
    public function unzipProjectFiles($archiveFileName)
    {
        $this->client->getSSH()->unzip($this->getSshPathToRoot() . '/' . $archiveFileName, $this->getSshPathToRoot());
    }

    /**
     * @param $sqlFile
     * @throws Exception\WebsiteInstallatorException
     */
    public function dumpSqlToDatabase($sqlFile)
    {
        $this->client->getMySQLSSH()->import($this->getSshPathToRoot() . '/' . $sqlFile);
    }
}
