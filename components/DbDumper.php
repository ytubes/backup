<?php
namespace ytubes\backup\components;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use ytubes\backup\databases\DatabaseInterface;

class DbDumper
{
    /**
     * @see \ytubes\backup\dbdumper\databases\DatabaseInterface
     */
    public $database;

    public function __construct(DatabaseInterface $databaseProvider)
    {
    	$this->database = $databaseProvider;
    }

    public function dumpToFile($dumpFile)
    {

    	$commandLine = $this->database->getDumpCommandLine($dumpFile);

		$this->process($commandLine);
    }

    public function restoreFromFile($dumpFile)
    {
    	$commandLine = $this->database->getRestoreCommandLine($dumpFile);

		$this->process($commandLine);
    }

    protected function process($commandLine)
    {
		$process = new Process($commandLine);
		$process->run();

			// executes after the command finishes
		if (!$process->isSuccessful()) {
		    throw new ProcessFailedException($process);
		}

		//echo $process->getOutput();
    }
}
