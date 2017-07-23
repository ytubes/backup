<?php
namespace ytubes\backup\databases;

/**
 * Class Database
 * @package BackupManager\Databases
 */
interface DatabaseInterface
{
    /**
     * @param array $config
     */
    public function setConfig($config);
    /**
     * @param string $inputPath
     * @return string
     */
    public function getDumpCommandLine($inputPath);
    /**
     * @param string $outputPath
     * @return string
     */
    public function getRestoreCommandLine($outputPath);
}