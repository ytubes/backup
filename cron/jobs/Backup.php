<?php
namespace ytubes\backup\cron\jobs;

use Yii;
use yii\helpers\FileHelper;
use yii\base\InvalidConfigException;
use ytubes\backup\Module;

/**
 * https://github.com/samdark/sitemap
 */
class Backup extends \yii\base\Object
{
	protected $module;

    public function __construct($config = [])
    {
        parent::__construct($config);

        if (!Yii::$app->hasModule('backup')) {
			throw new InvalidConfigException('Backup component not configured');
		}

		$this->module = Yii::$app->getModule('backup');
    }

    public function handle()
    {
		$this->module->dbManager->procedureBackup();
		$this->module->dbManager->deleteOldDumps();
		$this->module->fileManager->procedureBackup();
		$this->module->fileManager->deleteOldDumps();
    }

}
