<?php

/*
 * Vertiso (https://vertiso.pl)
 *
 * @copyright: Copyright (c) 2018 Vertiso (https://vertiso.pl)
 * @author Marcin Zagórski <vertiso851@gmail.com>
 */

namespace Vertiso\WebsiteInstallator\Connection;

use Touki\FTP\Connection\Connection;
use Touki\FTP\FTP;
use Touki\FTP\FTPFactory;

class FTPConnection
{
    private $connection;
    private $factory;
    private $tmpDirectory;

    /**
     * FTPConnection constructor.
     * @param $host
     * @param $username
     * @param $password
     * @param $tmpDirectory
     * @param int $port
     * @param int $timeout
     * @param bool $passiveMode
     */
    public function __construct($host, $username, $password, $tmpDirectory, $port = 21, $timeout = 90, $passiveMode = true)
    {
        $this->connection = new Connection($host, $username, $password, $port, $timeout, $passiveMode);
        $this->factory = new FTPFactory();
        $this->tmpDirectory = $tmpDirectory;
    }

    public function getFTP(): FTP
    {
        return $this->factory->build($this->connection);
    }

    public function getTemDirectory()
    {
        return $this->tmpDirectory;
    }
}
