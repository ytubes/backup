<?php
namespace ytubes\backup\controllers;

use Yii;
use yii\data\ArrayDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use ytubes\backup\Module;

/**
 * SettingsController implements the CRUD actions for Setting model.
 */
class EngineController extends Controller
{
    private $request = 'request';
    private $response = 'response';

    public function init()
    {
        parent::init();

        $this->request = \yii\di\Instance::ensure($this->request, \yii\web\Request::className());
        $this->response = \yii\di\Instance::ensure($this->response, \yii\web\Response::className());
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
	       'access' => [
	           'class' => AccessControl::className(),
               'rules' => [
                   [
                       'allow' => true,
                       'roles' => ['@'],
                       /*'matchCallback' => function ($rule, $action) {
                           return Yii::$app->user->identity->isAdmin;
                       }*/
                   ],
               ],
	       ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'create' => ['POST'],
                    'restore' => ['POST'],
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

	public function actionDownload($id)
	{
		$fileManager = Module::getInstance()->fileManager;
		$file = $fileManager->getFile($id);
		$this->response->xSendFile($file->getPathname());
	}

	public function actionRestore($id)
	{
		$fileManager = Module::getInstance()->fileManager;
		$fileManager->procedureRestore($id);

		$this->redirect(['/backup/index']);
	}

	public function actionDelete($id)
	{
		$fileManager = Module::getInstance()->fileManager;
		$file = $fileManager->getFile($id);
		@unlink($file);

		$this->redirect(['/backup/index']);
	}

	public function actionCreate()
	{
		$fileManager = Module::getInstance()->fileManager;
		$fileManager->procedureBackup();

		$this->redirect(['/backup/index']);
	}
}
