<?php

/*
 * Vertiso (https://vertiso.pl)
 *
 * @copyright: Copyright (c) 2018 Vertiso (https://vertiso.pl)
 * @author Marcin Zagórski <vertiso851@gmail.com>
 */

namespace Vertiso\WebsiteInstallator\Client;

use Vertiso\WebsiteInstallator\Connection\SSHConnection;
use phpseclib\Net\SSH2;

class SSHClient
{
    private $connection;
    private $output = [];

    public function __construct(SSHConnection $SSHConnection)
    {
        $this->connection = $SSHConnection;
    }

    /**
     * @return \phpseclib\Net\SSH2
     * @throws \Vertiso\WebsiteInstallator\Exception\WebsiteInstallatorException
     */
    public function getSSH(): SSH2
    {
        return $this->connection->getSSH();
    }

    /**
     * @param $archivePath
     * @param $destinationAbsolutePath
     * @return string
     * @throws \Vertiso\WebsiteInstallator\Exception\WebsiteInstallatorException
     */
    public function unzip($archivePath, $destinationAbsolutePath): string
    {
        $this->getSSH()->write('unzip -o ' . $archivePath . ' -d ' . $destinationAbsolutePath . '/' . "\n");

        return $this->getSSH()->read();
    }

    /**
     * @param $urlFile
     * @param $destinationAbsolutePath
     * @throws \Vertiso\WebsiteInstallator\Exception\WebsiteInstallatorException
     */
    public function download($urlFile, $destinationAbsolutePath)
    {
        $this->getSSH()->write('cd ' . $destinationAbsolutePath . ' && wget ' . $urlFile . "\n");

        return $this->getSSH()->read('100%');
    }

    /**
     * @param $absolutePath
     * @return string
     * @throws \Vertiso\WebsiteInstallator\Exception\WebsiteInstallatorException
     */
    public function remove($absolutePath)
    {
        $this->getSSH()->write('rm -rf ' . $absolutePath . "\n");

        return $this->getSSH()->read();
    }

    /**
     * @param $baseAbsolutePath
     * @param $destinationAbsolutePath
     * @return string
     * @throws \Vertiso\WebsiteInstallator\Exception\WebsiteInstallatorException
     */
    public function rename($baseAbsolutePath, $destinationAbsolutePath)
    {
        $this->getSSH()->write('mv ' . $baseAbsolutePath . ' ' . $destinationAbsolutePath . "\n");

        return $this->getSSH()->read();
    }

    public function getOutput()
    {
        return $this->output;
    }
}
