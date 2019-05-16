<?php

/*
 * Vertiso (https://vertiso.pl)
 *
 * @copyright: Copyright (c) 2018 Vertiso (https://vertiso.pl)
 * @author Marcin Zagórski <vertiso851@gmail.com>
 */

namespace Vertiso\WebsiteInstallator\Connection;

use Vertiso\WebsiteInstallator\Exception\WebsiteInstallatorException;
use phpseclib\Net\SSH2;

class MySQLSSHConnection
{
    const IMPORT_CHAR = '<';
    const EXPORT_CHAR = '>';

    private $SSHConnection;
    private $connected = false;

    private $dbHost;
    private $dbUsername;
    private $dbPassword;
    private $dbName;

    /**
     * MySQLConnection constructor.
     * @param SSHConnection $SSHConnection
     * @param $dbHost
     * @param $dbUsername
     * @param $dbPassword
     * @param $dbName
     */
    public function __construct(SSHConnection $SSHConnection, $dbHost, $dbUsername, $dbPassword, $dbName)
    {
        //Nowa instancja, dlatego że potrzebujemy "czystego" połączenia.
        // W innym połączeniu może być uruchomiony już jakiś skrypt.
        $this->SSHConnection = SSHConnection::newInstanceConnection($SSHConnection);

        $this->dbHost = $dbHost;
        $this->dbUsername = $dbUsername;
        $this->dbPassword = $dbPassword;
        $this->dbName = $dbName;
    }

    /**
     * @return SSH2
     * @throws \Vertiso\WebsiteInstallator\Exception\WebsiteInstallatorException
     */
    private function getSSH(): SSH2
    {
        return $this->SSHConnection->getSSH();
    }

    private function getConnectionCmd()
    {
        return 'mysql -h ' . $this->dbHost . ' -u ' . $this->dbUsername . ' -p -D' . $this->dbName;
    }

    /**
     * @param string $string
     * @return bool
     * @throws WebsiteInstallatorException
     */
    private function waitForPassword($string = 'Enter password: ')
    {
        $read = $this->getSSH()->read($string);

        return false !== strpos($read, $string);
    }

    /**
     * @throws WebsiteInstallatorException
     */
    private function enterPassword()
    {
        $this->getSSH()->write($this->dbPassword . "\n");
        $read = $this->getSSH()->read();

        if (false !== strpos($read, 'ERROR')) {
            throw new WebsiteInstallatorException('Failed MySQLSSH authenticated to database!');
        }

        return $read;
    }

    /**
     * @throws \Vertiso\WebsiteInstallator\Exception\WebsiteInstallatorException
     */
    public function doConnect()
    {
        if ($this->isConnected()) {
            throw new WebsiteInstallatorException('MySQL connection established!');
        }

        $this->getSSH()->write($this->getConnectionCmd() . "\n");

        if (!$this->waitForPassword()) {
            throw new WebsiteInstallatorException('MySQLSSH no wait for password!');
        }

        $this->enterPassword();
        $this->connected = true;
    }

    /**
     * @return bool
     */
    public function isConnected()
    {
        return $this->connected;
    }

    /**
     * @throws WebsiteInstallatorException
     */
    public function disconnect()
    {
        $this->getSSH()->write('exit' . "\n");
        $this->connected = false;
    }

    /**
     * @param $command
     * @return string
     * @throws \Vertiso\WebsiteInstallator\Exception\WebsiteInstallatorException
     */
    public function execute($command)
    {
        if (!$this->isConnected()) {
            $this->doConnect();
        }

        $this->getSSH()->write($command);

        return $this->getSSH()->read('Query OK');
    }

    /**
     * @param $char
     * @param $pathToFileOnServer
     * @return string
     * @throws WebsiteInstallatorException
     */
    private function importExport($char, $pathToFileOnServer)
    {
        if ($this->isConnected()) {
            $this->disconnect();
        }

        $this->getSSH()->write($this->getConnectionCmd() . ' ' . $char . ' ' . $pathToFileOnServer . "\n");

        if (!$this->waitForPassword()) {
            throw new WebsiteInstallatorException('ImportExport: No wait for password!');
        }

        return $this->enterPassword();
    }

    /**
     * @param $pathToFileOnServer
     * @return string
     * @throws WebsiteInstallatorException
     */
    public function import($pathToFileOnServer)
    {
        return $this->importExport(self::IMPORT_CHAR, $pathToFileOnServer);
    }

    /**
     * @param $pathToFileOnServer
     * @return string
     * @throws WebsiteInstallatorException
     */
    public function export($pathToFileOnServer)
    {
        return $this->importExport(self::EXPORT_CHAR, $pathToFileOnServer);
    }

    /**
     * @throws \Vertiso\WebsiteInstallator\Exception\WebsiteInstallatorException
     */
    public function __destruct()
    {
        $this->getSSH()->disconnect();
    }
}
