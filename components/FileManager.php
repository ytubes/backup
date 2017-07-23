<?php
namespace ytubes\backup\components;

use Yii;
use Symfony\Component\Finder\Finder;
use ytubes\backup\Module;
use ytubes\backup\components\base\DumpManager;

/**
 * Менеджер дампа файлов сайта.
 */
class FileManager extends DumpManager
{
	private $excludedDir = [
		'backup',
		'storage',
		'vendor',
		'runtime',
		'web/assets'
	];

	public function __construct($config = [])
	{
		$this->backupDirectory = Module::getInstance()->backupDirectory . '/engine';

		parent::__construct($config);
	}
	/**
	 * Процедура создания бекапа файлов сайта.
	 * @param string $dumpFile файл дампа.
	 * @return void
	 */
	public function procedureBackup()
	{
		// Get real path for our folder
		$rootPath = Yii::getAlias('@root');

		// Initialize archive object
		$zip = new \ZipArchive();
		$dumpFile = $this->generateDumpFilepath('file', 'zip');
		$zip->open($dumpFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

		// Create recursive directory iterator
		/** @var SplFileInfo[] $files */
		$finder = new Finder();
		$finder->files()
			->in($rootPath)
			->ignoreDotFiles(true)
			->exclude($this->excludedDir);

		foreach ($finder as $file) {
		        // Add current file to archive
		    $zip->addFile($file->getRealPath(), $file->getRelativePathname());
		}

			// Zip archive will be created only after closing object
		$zip->close();
	}
	/**
	 * Восстанавливает файлы сайта из бекапа.
	 * @param string $dumpFile файл дампа.
	 * @return void
	 */
	public function procedureRestore($dumpFilename)
	{
		// Get real path for our folder
		$rootPath = Yii::getAlias('@root');
		$file = $this->getFile($dumpFilename);

		$zip = new \ZipArchive;
		if ($zip->open($file->getPathname()) === true) {
		    $zip->extractTo($rootPath);
		    $zip->close();

		    echo 'ok';
		} else {
		    echo 'ошибка';
		}
	}
}
