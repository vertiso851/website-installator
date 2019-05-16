<?php

/*
 * Vertiso (https://vertiso.pl)
 *
 * @copyright: Copyright (c) 2018 Vertiso (https://vertiso.pl)
 * @author Marcin Zagórski <vertiso851@gmail.com>
 */

namespace Vertiso\WebsiteInstallator\Factory;

use Vertiso\WebsiteInstallator\Client\Client;
use Vertiso\WebsiteInstallator\Connection\FTPConnection;
use Vertiso\WebsiteInstallator\Connection\MySQLSSHConnection;
use Vertiso\WebsiteInstallator\Connection\SSHConnection;
use Vertiso\WebsiteInstallator\InstallationManager;

class InstallationManagerFactory
{
    public static function create(array $config): InstallationManager
    {
        $sshConn = new SSHConnection($config['ssh']['host'], $config['ssh']['port'], $config['ssh']['username'], $config['ssh']['password']);
        $mysqlsshConn = new MySQLSSHConnection(
            $sshConn,
            $config['mysql']['host'],
            $config['mysql']['username'],
            $config['mysql']['password'],
            $config['mysql']['name']
        );

        $ftpConn = new FTPConnection($config['ftp']['host'], $config['ftp']['username'], $config['ftp']['password'], $config['tmp']);
        $client = new Client($sshConn, $mysqlsshConn, $ftpConn);

        return new InstallationManager($client, $config['path']);
    }
}
