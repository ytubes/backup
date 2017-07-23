<?php
namespace ytubes\backup\components\base;

use Yii;
use yii\base\Security;
use yii\base\InvalidConfigException;
use yii\helpers\FileHelper;
use Symfony\Component\Finder\Finder;
use ytubes\backup\databases\MySql;
use ytubes\backup\Module;

/**
 * Основной класс бекап менеджера.
 */
abstract class DumpManager extends \yii\base\Component
{
	/**
	 * @var string Директория бекапов сайта.
	 */
	protected $backupDirectory;
	/**
	 * Процедура создания бекапа. Дампит базу в файл.
	 * @param string $dumpFile файл дампа базы.
	 * @return void
	 */
	abstract public function procedureBackup();
	/**
	 * Восстанавливает базу из бекапа.
	 * @param string $dumpFile файл дампа базы.
	 * @return void
	 */
	abstract public function procedureRestore($dumpFilename);
	/**
	 * Удаляет старые файлы. Если количество файлов превышает 7 штук, а срок более недели - удаляем.
	 *
	 * @return void
	 */
	public function deleteOldDumps()
	{
		$weekAgoTime = time() - (60 * 60 * 24 * 7);

		$files = $this->getDumpFiles();

		$deletedCounter = 0; // счетчик удаленных файлов
		$filesCounter = 0; // счетчик файлов
		foreach ($files as $file) {
	        if ($filesCounter >= 7 && $file->getMTime() > $weekAgoTime) {
	        	if (false === @unlink($file->getPathname())) {
	        		// тут лог написать что не удалось удалить.
	        		continue;
	        	}

	        	$deletedCounter ++;
	        }

	        $filesCounter++;
		}

		return $deletedCounter;
	}
	/**
	 * Генерирует путь к файлу дампа. Его имя и создает директории к пути, если необходимо.
	 * @param string $suffix название файла (суфикс к дате)
	 * @param string $extension расширение будущего дампа
	 * @return string
	 */
	protected function generateDumpFilepath($suffix = '', $extension = 'txt')
	{
		$date = gmdate('Y-m-d');

		if (!is_dir($this->backupDirectory)) {
			FileHelper::createDirectory($this->backupDirectory, 0755);
		}

		$security = new Security();
		$randString = $security->generateRandomString(8);
		$dumpFilename = $date . '_' . (!empty($suffix) ? $suffix . '_' : '') . $randString . '.' . $extension;

		return $this->backupDirectory . DIRECTORY_SEPARATOR . $dumpFilename;
	}
	/**
	 * Получает список файлов из директории бекапа.
	 * @return array
	 */
	public function getDumpFiles()
	{
			// Сортирует по времени модификации в обратном порядке
		$sort = function ($a, $b) {
            return $b->getMTime() - $a->getMTime();
        };

		$finder = new Finder();
		$finder->files()
			->ignoreDotFiles(true)
			->in($this->backupDirectory)
			->sort($sort)
			->depth('== 0');

		return $finder;
	}
	/**
	 * Получение файла бекапа по его имени в директории бекапа.
	 * @param string $filename
	 * @return object \SplFileInfo
	 */
	public function getFile($filename)
	{
		$filepath = $this->backupDirectory . DIRECTORY_SEPARATOR . $filename;
		$file = new \SplFileInfo($filepath);

		if (!$file->isFile()) {
			throw new InvalidConfigException("File {$filename} not exists");
		}

		return $file;
	}
}
