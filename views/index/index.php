<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Резервное копирование';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="box box-default">
            <div class="box-header with-border">
                <i class="fa fa-database text-maroon-disabled"></i><h3 class="box-title">Резервные копии базы данных</h3>
            </div>

            <?php $form = ActiveForm::begin([
                'action' => Url::to(['index']),
            ]); ?>

                <div class="box-body pad">
                    <?php echo GridView::widget([
                        // полученные данные
                        'dataProvider' => $dbDataProvider,
                        'options' => [
                            'class' => 'table table-striped table-bordered table-responsive'
                        ],
                        'columns' => [
                            [
                                'label' => 'Дата создания',
                                'attribute' => 'modiy_time',
                                'value' => function($model) {
                                    return Yii::$app->formatter->asDateTime($model['modiy_time']);
                                },
                                'headerOptions' => ['style' => 'width:165px;'],
                            ],
                            [
                                'label' => 'Файл', // название столбца
                                'attribute' => 'filename', // атрибут
                            ],
                            [
                                'label' => 'Размер',
                                'attribute' => 'file_size',
                                'value' => function($model) {
                                    return Yii::$app->formatter->asShortSize($model['file_size'], 2);
                                },
                                'headerOptions' => ['style' => 'width:90px;'],
                            ],
                            [
                                'label' => 'Компрессия',
                                'attribute' => 'is_archive',
                                'value' => function($model) {
                                    return ($model['is_archive']) ? '<i class="fa fa-file-archive-o text-green"></i>' : '<span class="text-red">(none)</span>';
                                },
                                'format' => 'html',
                                'headerOptions' => ['style' => 'width:90px;'],
                            ],
                            [
                                'class' => yii\grid\ActionColumn::className(),
                                'template' => '
                                    <ul class="action-buttons pull-right">
                                        <li class="action-buttons__item">{download}</li>
                                        <li class="action-buttons__item">{restore}</li>
                                        <li class="action-buttons__item">{delete}</li>
                                    </ul>
                                ',
                                'buttons' => [
                                    'download' => function ($url, $model, $key) {
                                        return Html::a('<span class="glyphicon glyphicon-download-alt"></span>', ['/backup/database/download', 'id' => $model['filename']], ['title' => 'Скачать файл']);
                                    },
                                    'restore' => function ($url, $model, $key) {
                                        return Html::a('<span class="glyphicon glyphicon-import text-aqua"></span>', ['/backup/database/restore', 'id' => $model['filename']], [
                                            'title' => 'Восстановить дамп',
                                            'data' => [
                                                'confirm' => 'Уверены?',
                                                'method' => 'POST',
                                            ],
                                        ]);
                                    },
                                    'delete' => function ($url, $model, $key) {
                                        return Html::a('<span class="glyphicon glyphicon-trash text-red"></span>', ['/backup/database/delete', 'id' => $model['filename']], [
                                            'title' => 'Удалить файл',
                                            'data' => [
                                                'confirm' => 'Действительно хотите удалить этот файл?',
                                                'method' => 'POST',
                                            ],
                                        ]);
                                    },
                                ],
                                'headerOptions' => ['style' => 'width:90px;'],
                            ],
                       ],
                    ]); ?>
                </div>

                <div class="box-footer clearfix">
                    <div class="form-group">
                        <?= Html::a('<i class="glyphicon glyphicon-export"></i> Создать копию базы данных', ['database/create'], [
                            'class' => 'btn btn-warning',
                            'title' => 'Создать бекап',
                            'data' => [
                                'method' => 'POST',
                            ],
                        ]) ?>
                    </div>
                </div>

            <?php ActiveForm::end(); ?>

        </div>

        <div class="box box-default">
            <div class="box-header with-border">
                <i class="fa fa-files-o text-maroon-disabled"></i><h3 class="box-title">Резервные копии файлов сайта (без контента)</h3>
            </div>

            <?php $form = ActiveForm::begin([
                'action' => Url::to(['index']),
            ]); ?>

                <div class="box-body pad">
                    <?php echo GridView::widget([
                        // полученные данные
                        'dataProvider' => $filesDataProvider,
                        'options' => [
                            'class' => 'table table-striped table-bordered table-responsive'
                        ],
                        'columns' => [
                            [
                                'label' => 'Дата создания',
                                'attribute' => 'modiy_time',
                                'value' => function($model) {
                                    return Yii::$app->formatter->asDateTime($model['modiy_time']);
                                },
                                'headerOptions' => ['style' => 'width:165px;'],
                            ],
                            [
                                'label' => 'Файл', // название столбца
                                'attribute' => 'filename', // атрибут
                            ],
                            [
                                'label' => 'Размер',
                                'attribute' => 'file_size',
                                'value' => function($model) {
                                    return Yii::$app->formatter->asShortSize($model['file_size'], 2);
                                },
                                'headerOptions' => ['style' => 'width:90px;'],
                            ],
                            [
                                'label' => 'Компрессия',
                                'attribute' => 'is_archive',
                                'value' => function($model) {
                                    return ($model['is_archive']) ? '<i class="fa fa-file-archive-o text-green"></i>' : '<span class="text-red">(none)</span>';
                                },
                                'format' => 'html',
                                'headerOptions' => ['style' => 'width:90px;'],
                            ],
                            [
                                'class' => yii\grid\ActionColumn::className(),
                                'template' => '
                                    <ul class="action-buttons pull-right">
                                        <li class="action-buttons__item">{download}</li>
                                        <li class="action-buttons__item">{restore}</li>
                                        <li class="action-buttons__item">{delete}</li>
                                    </ul>
                                ',
                                'buttons' => [
                                    'download' => function ($url, $model, $key) {
                                        return Html::a('<span class="glyphicon glyphicon-download-alt"></span>', ['/backup/engine/download', 'id' => $model['filename']], ['title' => 'Скачать файл']);
                                    },
                                    'restore' => function ($url, $model, $key) {
                                        return Html::a('<span class="glyphicon glyphicon-import text-aqua"></span>', ['/backup/engine/restore', 'id' => $model['filename']], [
                                            'title' => 'Восстановить дамп',
                                            'data' => [
                                                'confirm' => 'Уверены?',
                                                'method' => 'POST',
                                            ],
                                        ]);
                                    },
                                    'delete' => function ($url, $model, $key) {
                                        return Html::a('<span class="glyphicon glyphicon-trash text-red"></span>', ['/backup/engine/delete', 'id' => $model['filename']], [
                                            'title' => 'Удалить файл',
                                            'data' => [
                                                'confirm' => 'Действительно хотите удалить этот файл?',
                                                'method' => 'POST',
                                            ],
                                        ]);
                                    },
                                ],
                                'headerOptions' => ['style' => 'width:90px;'],
                            ],
                       ],
                    ]); ?>
                </div>

                <div class="box-footer clearfix">
                    <div class="form-group">
                        <?= Html::a('<i class="glyphicon glyphicon-export"></i> Создать копию файлов сайта', ['engine/create'], [
                            'class' => 'btn btn-warning',
                            'title' => 'Создать бекап',
                            'data' => [
                                'method' => 'POST',
                            ],
                        ]) ?>
                    </div>
                </div>

            <?php ActiveForm::end(); ?>

        </div>

    </div>
</div>
