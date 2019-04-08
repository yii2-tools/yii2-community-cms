<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 21.03.2016 16:41
 * via Gii Module Generator
 */

use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\grid\GridView;
use app\helpers\ModuleHelper;
use app\helpers\RouteHelper;
use app\helpers\Html;
use admin\modules\widgets\assets\WidgetsAsset;

$this->title = Yii::t(ModuleHelper::ADMIN_WIDGETS, 'Widgets management');
WidgetsAsset::register($this);

?>

<?= Html::beginTag('div', ['class' => 'row']) ?>
    <?= Html::beginTag('div', ['class' => 'col-md-12']) ?>
        <?php $form = ActiveForm::begin(['action' => 'widgets/get']); ?>
            <?= Html::submitButton(Yii::t('app', 'Update'), ['class' => 'btn btn-primary col-xs-3 pull-right']) ?>
        <?php ActiveForm::end() ?>
    <?= Html::endTag('div'); ?>
<?= Html::endTag('div'); ?>

<?= Html::beginTag('div', ['class' => 'row margin-top-10']) ?>
    <?= Html::beginTag('div', ['class' => 'col-md-12']) ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            //'filterModel'  	=> $searchModel,
            'layout' => "{items}\n{pager}",
            'emptyText' => Yii::t(ModuleHelper::ADMIN_WIDGETS, 'No widgets available'),
            'pjax' => true,
            'options' => [
                'class' => 'table-widgets',
            ],
            'rowOptions' => function ($model, $index, $widget, $grid) {
                    if (!isset($model['status']) || intval($model['status']) < 1) {
                        return [];
                    }
                    return $model['current_version'] == $model['active_version']
                        ? ['class' => 'success']
                        : ['class' => 'warning'];
                },
            'columns' => [
                [
                    'attribute' => 'title',
                    'label'     => Yii::t('app', 'Name'),
                    'value'     => function ($model) {
                        return $this->render('/_name', ['model' => $model]);
                    },
                    'options'   => [
                        'class' => 'col-md-2'
                    ],
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'descr',
                    'label'     => Yii::t('app', 'Description'),
                    'options'   => [
                        'class' => 'col-md-5'
                    ],
                    'format' => 'html',
                ],
                [
                    'attribute' => 'current_version',
                    'label'     => Yii::t('app', 'Version'),
                    'noWrap'    => true,
                    'value' => function ($model) {
                            $formatter = Yii::$app->getFormatter();
                            $currentVersion = $formatter->asVersionString($model['current_version']);
                            if (isset($model['status']) && intval($model['status']) > 0) {
                                if (isset($model['active_version']) && intval($model['active_version']) > 0 &&
                                    $model['current_version'] != $model['active_version']) {
                                    $activeVersion = $formatter->asVersionString($model['active_version']);
                                    return $activeVersion . ' <br />(' . $currentVersion . ')';
                                }
                            }
                            return $currentVersion;
                        },
                    'options'   => [
                        'class' => 'col-md-1'
                    ],
                    'format' => 'html',
                ],
                [
                    'attribute' => 'release_dt',
                    'label'     => Yii::t('app', 'Release date'),
                    'hAlign'    => GridView::ALIGN_CENTER,
                    'noWrap'    => true,
                    'value' => function ($model) {
                            return Yii::$app->getFormatter()->asDate($model['release_dt']);
                        },
                    'filterType' => GridView::FILTER_DATE,
                    'options'   => [
                        'class' => 'col-md-2'
                    ],
                ],
                [
                    'class' => 'kartik\grid\ActionColumn',
                    'template' => '{process} {add} {update} {delete}',
                    'buttons' => [
                        'add' => function ($url, $model, $key) {
                                if (isset($model['status']) && intval($model['status']) > 0) {
                                    return '';
                                }
                                return Html::a(Html::icon('cloud-download'), $url, [
                                        'title' => Yii::t('app', 'Add'),
                                        'aria-label' => Yii::t('app', 'Add'),
                                        'data' => [
                                            'method' => 'post',
                                            'params' => ['widget_key' => $model['widget_key']],
                                            'pjax' => true,
                                        ]
                                    ]);
                            },
                        'update' => function ($url, $model, $key) {
                                if (!isset($model['status']) || intval($model['status']) !== 1
                                    || $model['active_version'] == $model['current_version']) {
                                    return '';
                                }
                                return Html::a(Html::icon('refresh'), $url, [
                                        'title' => Yii::t('app', 'Update'),
                                        'aria-label' => Yii::t('app', 'Update'),
                                        'data' => [
                                            'method' => 'post',
                                            'params'=> ['widget_key' => $model['widget_key']],
                                            'pjax' => true,
                                        ]
                                    ]);
                            },
                        'delete' => function ($url, $model, $key) {
                                if (!isset($model['status']) || intval($model['status']) !== 1) {
                                    return '';
                                }
                                return Html::a(Html::icon('trash'), $url, [
                                        'title' => Yii::t('app', 'Delete'),
                                        'aria-label' => Yii::t('app', 'Delete'),
                                        'data' => [
                                            'method' => 'post',
                                            'confirm' => Yii::t('app', 'Are you sure?'),
                                            'params'=> ['widget_key' => $model['widget_key']],
                                            'pjax' => true,
                                        ]
                                    ]);
                            },
                        'process' => function ($url, $model, $key) {
                                if (!isset($model['status']) || intval($model['status']) < 2) {
                                    return '';
                                }
                                return Html::faIcon('refresh', ['spin' => true]);
                            }
                    ],
                    'urlCreator' => function ($action, $model) {
                            return Url::to([RouteHelper::ADMIN_WIDGETS_MANAGEMENT_CONTROLLER . '/' . $action]);
                        },
                ],
            ]
        ]) ?>
    <?= Html::endTag('div'); ?>
<?= Html::endTag('div'); ?>
