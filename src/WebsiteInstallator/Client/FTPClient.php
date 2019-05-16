<?php

/*
 * Vertiso (https://vertiso.pl)
 *
 * @copyright: Copyright (c) 2018 Vertiso (https://vertiso.pl)
 * @author Marcin Zagórski <vertiso851@gmail.com>
 */

namespace Vertiso\WebsiteInstallator\Client;

use Vertiso\WebsiteInstallator\Connection\FTPConnection;
use Vertiso\WebsiteInstallator\Exception\WebsiteInstallatorException;
use Touki\FTP\FTP;
use Touki\FTP\Model\Directory;
use Touki\FTP\Model\File;

class FTPClient
{
    private $connection;

    public function __construct(FTPConnection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return \Touki\FTP\FTP
     */
    private function getFTP()
    {
        return $this->connection->getFTP();
    }

    /**
     * @param $path
     * @return bool
     */
    public function directoryExists($path)
    {
        if ($this->getFTP()->directoryExists(new Directory($path))) {
            return true;
        }

        return false;
    }

    /**
     * @param $file
     * @return File|null
     */
    public function findFile($file)
    {
        $file = $this->getFTP()->findFileByName($file);

        if ($file) {
            return $file;
        }

        return null;
    }

    /**
     * @param $localName
     * @param File $file
     * @return bool
     * @throws \Touki\FTP\Exception\DirectoryException
     */
    public function download($localName, File $file)
    {
        if ($this->getFTP()->download($localName, $file)) {
            return true;
        }

        return false;
    }

    /**
     * @param $uploadTo
     * @param $filePath
     * @param array $options
     * @return bool
     */
    public function upload($uploadTo, $filePath, $options = [])
    {
        if ($this->getFTP()->upload(new File($uploadTo), $filePath, $options)) {
            return true;
        }

        return false;
    }

    /**
     * @param array $files
     */
    public function deleteFiles(array $files)
    {
        if (count($files) > 0) {
            foreach ($files as $file) {
                $this->getFTP()->delete(new File($file));
            }
        }
    }

    /**
     * @param string $directoryPath
     * @return array
     */
    public function listFiles($directoryPath = '/')
    {
        try {
            return $this->getFTP()->findFilesystems(new Directory($directoryPath));
        } catch (\Exception $exception) {
            new WebsiteInstallatorException('FTP Manager Exception! ' . $exception->getMessage());
        }
    }

    public function newFile($absoluteFileName, $content)
    {
        $tmpFilePath = $this->connection->getTemDirectory() . '/' . time();
        @file_put_contents($tmpFilePath, $content);
        $this->upload($absoluteFileName, $tmpFilePath, [
            FTP::NON_BLOCKING => true,
        ]);
        @unlink($tmpFilePath);
    }
}
