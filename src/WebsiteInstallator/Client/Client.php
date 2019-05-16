<?php

/*
 * Vertiso (https://vertiso.pl)
 *
 * @copyright: Copyright (c) 2018 Vertiso (https://vertiso.pl)
 * @author Marcin Zagórski <vertiso851@gmail.com>
 */

namespace Vertiso\WebsiteInstallator\Client;

use Vertiso\WebsiteInstallator\Connection\FTPConnection;
use Vertiso\WebsiteInstallator\Connection\MySQLSSHConnection;
use Vertiso\WebsiteInstallator\Connection\SSHConnection;

class Client
{
    private $SSHConnection;
    private $mySQLSSHConnection;
    private $FTPConnection;

    public function __construct(SSHConnection $SSHConnection, MySQLSSHConnection $mySQLSSHConnection, FTPConnection $FTPConnection)
    {
        $this->SSHConnection = $SSHConnection;
        $this->mySQLSSHConnection = $mySQLSSHConnection;
        $this->FTPConnection = $FTPConnection;
    }

    /**
     * @return FTPClient
     */
    public function getFTP()
    {
        return new FTPClient($this->FTPConnection);
    }

    /**
     * @return SSHClient
     */
    public function getSSH()
    {
        return new SSHClient($this->SSHConnection);
    }

    /**
     * @return MySQLSSHClient
     */
    public function getMySQLSSH()
    {
        return new MySQLSSHClient($this->mySQLSSHConnection);
    }
}
