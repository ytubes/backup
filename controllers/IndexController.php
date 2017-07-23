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
class IndexController extends Controller
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

    /**
     * Lists all Setting models.
     * @return mixed
     */
    public function actionIndex()
    {
		$dbDumpFiles = Module::getInstance()->dbManager->getDumpFiles();

		$dbDataProvider = new ArrayDataProvider([
			'allModels' => $this->prepareItems($dbDumpFiles),
			//'keys' => ['filename'],
			'sort' => [ // подключаем сортировку
				'attributes' => ['modiy_time', 'filename', 'file_size', 'is_archive'],
			],
		]);

		$engineDumpFiles = Module::getInstance()->fileManager->getDumpFiles();

		$filesDataProvider = new ArrayDataProvider([
			'allModels' => $this->prepareItems($engineDumpFiles),
			//'keys' => ['filename'],
			'sort' => [ // подключаем сортировку
				'attributes' => ['modiy_time', 'filename', 'file_size', 'is_archive'],
			],
		]);

		return $this->render('index', [
            'dbDataProvider' => $dbDataProvider,
            'filesDataProvider' => $filesDataProvider,
        ]);
    }

    protected function prepareItems($files)
    {
    	$items = [];
    	foreach ($files as $file) {
    		$filename = $file->getFilename();
    		$items[$filename]['modiy_time'] = $file->getMTime();
    		$items[$filename]['filename'] = $filename;
    		$items[$filename]['file_size'] = $file->getSize();
    		$items[$filename]['is_archive'] = (in_array($file->getExtension(), ['gz', 'zip', 'tar']));
    	}

    	return $items;
    }
}
