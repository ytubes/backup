<?php
namespace ytubes\backup\components;

use Yii;
use ytubes\backup\databases\MySql;
use ytubes\backup\Module;
use ytubes\backup\components\base\DumpManager;

/**
 * Менеджер дампа базы данных.
 */
class DbManager extends DumpManager
{
	public function __construct($config = [])
	{
		$this->backupDirectory = Module::getInstance()->backupDirectory . '/databases';

		parent::__construct($config);
	}
	/**
	 * Процедура создания бекапа. Дампит базу в файл.
	 * @param string $dumpFile файл дампа базы.
	 * @return void
	 */
	public function procedureBackup()
	{
		$dbConfig = $this->getDbConfig();

		$databaseProvider = new Mysql();
		$databaseProvider->setConfig($dbConfig);

		$dumper = new DbDumper($databaseProvider);

		$dumpFile = $this->generateDumpFilepath($dbConfig['dbName'], 'sql');

		$dumper->dumpToFile($dumpFile);
	}
	/**
	 * Восстанавливает базу из бекапа.
	 * @param string $dumpFile файл дампа базы.
	 * @return void
	 */
	public function procedureRestore($dumpFilename)
	{
		$file = $this->getFile($dumpFilename);

		$dbConfig = $this->getDbConfig();

		if ($file->getExtension() === 'sql') {
			$dbConfig['enableCompression'] = false;
		} else {
			$dbConfig['enableCompression'] = true;
		}

		$databaseProvider = new Mysql();
		$databaseProvider->setConfig($dbConfig);

		$dumper = new DbDumper($databaseProvider);

		$dumper->restoreFromFile($file->getPathname());
	}
	/**
	 * Вытаскивает конфиг мускула из текущих настроек бд.
	 *
	 * @return array
	 */
	protected function getDbConfig()
	{
		$db = Yii::$app->db;

		$config = [];
		$config['user'] = $db->username;
		$config['password'] = $db->password;


		if (preg_match('/dbname=(\w+)?;?+/s', $db->dsn, $matches)) {
			$config['dbName'] = $matches[1];
		}

		if (!empty($db->charset)) {
			$config['defaultCharacterSet'] = $db->charset;
		}

		$config['enableCompression'] = Module::getInstance()->enableCompression;

		return $config;
	}
}
