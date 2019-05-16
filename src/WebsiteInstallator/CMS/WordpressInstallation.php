<?php

/*
 * Vertiso (https://vertiso.pl)
 *
 * @copyright: Copyright (c) 2018 Vertiso (https://vertiso.pl)
 * @author Marcin Zagórski <vertiso851@gmail.com>
 */

namespace Vertiso\WebsiteInstallator\CMS;

use Vertiso\WebsiteInstallator\Exception\WebsiteInstallatorException;
use Vertiso\WebsiteInstallator\InstallationManager;

class WordpressInstallation
{
    private $installationManager;
    private $wordpressQueryBuilder;
    private $changes = [];

    /**
     * WordpressInstallation constructor.
     * @param InstallationManager $installationManager
     */
    public function __construct(InstallationManager $installationManager)
    {
        $this->installationManager = $installationManager;
        $this->wordpressQueryBuilder = new WordpressQueryBuilder();
    }

    /**
     * @param $title
     */
    public function setWebsiteTitle($title)
    {
        $this->changes['websiteTitle'] = compact('title');
    }

    /**
     * @param $url
     */
    public function setHomeUrl($url)
    {
        $this->changes['homeUrl'] = compact('url');
    }

    /**
     * @param $displayName
     * @param $userNiceName
     * @param $login
     * @param $password
     * @param $email
     */
    public function setUser($displayName, $userNiceName, $login, $password, $email)
    {
        $this->changes['user'] = compact('displayName', 'userNiceName', 'login', 'password', 'email');
    }

    /**
     * @param WordpressConfiguration $configuration
     */
    public function setConfigurationFiles(WordpressConfiguration $configuration)
    {
        $this->changes['configurationFiles'] = $configuration;
    }

    /**
     * @param array $data
     * @throws \Vertiso\WebsiteInstallator\Exception\WebsiteInstallatorException
     */
    protected function changeWebsiteTitle(array $data)
    {
        $this->installationManager->getClient()->getMySQLSSH()->query(
            $this->wordpressQueryBuilder->updateWebsiteTitle($data['title'])
        );
    }

    /**
     * @param array $data
     * @throws \Vertiso\WebsiteInstallator\Exception\WebsiteInstallatorException
     */
    protected function changeHomeUrl(array $data)
    {
        $this->installationManager->getClient()->getMySQLSSH()->query(
            $this->wordpressQueryBuilder->updateHomeUrl($data['url'])
        );
    }

    /**
     * @param array $data
     * @throws \Vertiso\WebsiteInstallator\Exception\WebsiteInstallatorException
     */
    protected function changeUser(array $data)
    {
        $this->installationManager->getClient()->getMySQLSSH()->query(
            $this->wordpressQueryBuilder->updateUser(
                $data['displayName'], $data['userNiceName'], $data['login'], md5($data['password']), $data['email'], 2
            )
        );
    }

    protected function changeConfigurationFiles(WordpressConfiguration $configuration)
    {
        foreach ($configuration->getConfigurationFilesContents() as $fileName => $fileContent) {
            $this->installationManager->getClient()->getFTP()->newFile(
                $this->installationManager->getFtpPathToRoot() . '/' . $fileName,
                $fileContent
            );
        }
    }

    /**
     * @throws WebsiteInstallatorException
     */
    public function doChanges()
    {
        if (count($this->changes)) {
            foreach ($this->changes as $changeEvent => $data) {
                $methodName = 'change' . ucfirst($changeEvent);
                if (!method_exists($this, $methodName)) {
                    throw new WebsiteInstallatorException('WordpressInstallator: Method ' . $methodName . ' not exists!');
                }

                $this->$methodName($data);
            }
        }
    }

    /**
     * @param $sourceUrl
     * @param $archiveFileName
     * @param $sqlFile
     * @throws WebsiteInstallatorException
     */
    public function install($sourceUrl, $archiveFileName, $sqlFile)
    {
        $this->installationManager->removeAllFilesFromRootDirectory();
        $this->installationManager->pushProjectFilesToServer($sourceUrl, $archiveFileName);
        $this->installationManager->unzipProjectFiles($archiveFileName);
        $this->installationManager->dumpSqlToDatabase($sqlFile);

        $this->doChanges();
    }
}
