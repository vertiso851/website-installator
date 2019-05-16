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

class SSHConnection
{
    private $host;
    private $port;
    private $username;
    private $password;

    private $connection;

    /**
     * @param SSHConnection $connection
     * @return SSHConnection
     */
    public static function newInstanceConnection(SSHConnection $connection)
    {
        return new self($connection->getHost(), $connection->getPort(), $connection->getUsername(), $connection->getPassword());
    }

    /**
     * SSHConnection constructor.
     * @param $host
     * @param $port
     * @param $username
     * @param $password
     */
    public function __construct($host, $port, $username, $password)
    {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @return SSH2
     * @throws WebsiteInstallatorException
     */
    private function connect()
    {
        $ssh = new SSH2($this->host, $this->port, 3);
        if (!$ssh->login($this->username, $this->password)) {
            throw new WebsiteInstallatorException('SSH login failed!');
        }

        return $ssh;
    }

    /**
     * @return SSH2
     * @throws WebsiteInstallatorException
     */
    public function getSSH(): SSH2
    {
        if (!$this->connection instanceof SSH2 || !$this->connection->isConnected()) {
            $this->connection = $this->connect();
        }

        return $this->connection;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    public function __destruct()
    {
        if ($this->connection instanceof SSH2 && $this->connection->isConnected()) {
            $this->connection->disconnect();
        }

        $this->connection = null;
    }
}
