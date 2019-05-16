<?php

/*
 * Vertiso (https://vertiso.pl)
 *
 * @copyright: Copyright (c) 2018 Vertiso (https://vertiso.pl)
 * @author Marcin Zagórski <vertiso851@gmail.com>
 */

namespace Vertiso\WebsiteInstallator\Client;

use Vertiso\WebsiteInstallator\Connection\MySQLSSHConnection;

class MySQLSSHClient
{
    private $connection;
    private $output = [];

    public function __construct(MySQLSSHConnection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param $query
     * @return string
     * @throws \Vertiso\WebsiteInstallator\Exception\WebsiteInstallatorException
     */
    public function query($query)
    {
        $this->connection->execute($query . ';' . "\n");
    }

    /**
     * @param $pathToFileOnServer
     * @return string
     * @throws \Vertiso\WebsiteInstallator\Exception\WebsiteInstallatorException
     */
    public function import($pathToFileOnServer)
    {
        return $this->connection->import($pathToFileOnServer);
    }
}
