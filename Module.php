<?php

namespace ytubes\backup;

use Yii;

/**
 * videos module definition class
 */
class Module extends \ytubes\components\Module
{
    /**
     * @inheritdoc
     */
	public $name = 'Бекап';
    /**
     * @inheritdoc
     */
	public $description = 'Модуль создания бекапа базы данных';
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'ytubes\backup\controllers';

    /**
     * @inheritdoc
     */
    public $defaultRoute = 'index';
    /**
     * @var string
     */
    public $enableCompression = false;
    /**
     * @var string
     */
    public $backupDirectory = 'backup';
    /**
     * @inheritdoc
     */
    /*public function init()
    {
        parent::init();

    }*/

    public function getName()
    {
    	return $this->name;
    }

    public function getDescription()
    {
    	return $this->description;
    }

    public function getId()
    {
    	return $this->id;
    }

	public function init()
	{
	    parent::init();
	    	// initialize the module with the configuration loaded from config.php
	    Yii::configure($this, require(__DIR__ . '/config/components.php'));
	}
}
