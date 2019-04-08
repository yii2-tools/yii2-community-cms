<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 21.03.2016 16:39
 *
 * @var \yii\web\View $this
 * @var \site\modules\design\Module $designModule
 */

use yii\widgets\ActiveForm;
use yii\helpers\Url;
use dosamigos\fileupload\FileUpload;
use kartik\grid\GridView;
use app\helpers\Html;
use app\helpers\RouteHelper;
use site\modules\design\helpers\ModuleHelper;

$this->title = Yii::t(ModuleHelper::ADMIN_DESIGN, 'Design packs management');

?>

<?= Html::beginTag('div', ['class' => 'row margin-top-10']) ?>
    <?= Html::beginTag('div', ['class' => 'col-md-12']) ?>
        <?php $form = ActiveForm::begin([
                'id' => 'upload-form',
                'action' => [RouteHelper::ADMIN_DESIGN_PACKS_IMPORT],
                'options' => ['class' => 'form-horizontal'],
                'fieldConfig' => [
                    'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-12 text-center\">{error}\n{hint}</div>",
                    'labelOptions' => ['class' => 'col-md-6 control-label'],
                ],
                'enableAjaxValidation'   => true,
                'enableClientValidation' => false,
                'validateOnBlur'         => false,
            ]); ?>

            <?= $form->field($uploadForm, 'file')
                ->label(Yii::t(ModuleHelper::ADMIN_DESIGN, 'Import design pack'))
                ->widget(FileUpload::className(), [
                    'model' => $uploadForm,
                    'attribute' => 'file',
                    'url' => [RouteHelper::ADMIN_DESIGN_PACKS_IMPORT],
                    'clientEvents' => [
                        'fileuploaddone' => 'function(e, data) {
                            if ("undefined" !== typeof data.result.errors.file) {
                                $("#upload-form").yiiActiveForm("updateMessages", {
                                    "uploadform-file": data.result.errors.file
                                }, true);
                            } else {
                                location.reload();
                            }
                        }',
                    ],
                ]) ?>

            <div class="form-group" style="display:none">
                <div class="col-md-offset-5 col-md-2">
                    <?= Html::submitButton(Yii::t('app', 'Submit'),
                        ['class' => 'btn btn-block btn-success']) ?>
                </div>
            </div>

        <?php ActiveForm::end() ?>
    <?= Html::endTag('div') ?>
<?= Html::endTag('div') ?>

<?= Html::beginTag('div', ['class' => 'row']) ?>
    <?= Html::beginTag('div', ['class' => 'col-md-12']) ?>
        <?= GridView::widget([
                'dataProvider' => $dataProvider,
                //'filterModel'  	=> $searchModel,
                'layout' => "{items}\n{pager}",
                'emptyText' => Yii::t(ModuleHelper::ADMIN_DESIGN, 'No design packs available'),
                'pjax' => true,
                'options' => [
                    'class' => 'table-plugins',
                ],
                'columns' => [
                    [
                        'attribute' => 'title',
                        'label'     => Yii::t('app', 'Name'),
                        'value'     => function ($model) {
                            return $this->render('@admin/modules/design/modules/packs/views/_name', ['model' => $model]);
                        },
                        'options'   => [
                            'class' => 'col-md-3'
                        ],
                        'format'    => 'raw',
                    ],
                    [
                        'attribute' => 'description',
                        'label'     => Yii::t('app', 'Description'),
                        'options'   => [
                            'class' => 'col-md-5'
                        ],
                        'format' => 'html',
                    ],
                    [
                        'attribute' => 'version',
                        'label'     => Yii::t('app', 'Version'),
                        'noWrap'    => true,
                        'options'   => [
                            'class' => 'col-md-1'
                        ],
                        'format' => 'html',
                    ],
                    [
                        'attribute' => 'uploaded_at',
                        'label'     => Yii::t('app', 'Uploaded At'),
                        'hAlign'    => GridView::ALIGN_CENTER,
                        'noWrap'    => true,
                        'value' => function ($model) {
                            return Yii::$app->getFormatter()->asDate($model['uploaded_at']);
                        },
                        'filterType' => GridView::FILTER_DATE,
                        'options'   => [
                            'class' => 'col-md-2'
                        ],
                    ],
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'template' => '{edit} {export} {delete}',
                        'buttons' => [
                            'edit' => function ($url, $model, $key) {
                                // @todo uncomment edit button after action implementation (in 2.0.x)
                                return '';

//                                return Html::a(Html::faIcon('pencil'), $url, [
//                                    'title' => Yii::t('app', 'Edit'),
//                                    'aria-label' => Yii::t('app', 'Edit'),
//                                    'data' => [
//                                        'method' => 'post',
//                                        'params'=> ['name' => $model['name']],
//                                    ]
//                                ]);
                            },
                            'export' => function ($url, $model, $key) {
                                return Html::a(Html::faIcon('download'), $url, [
                                    'title' => Yii::t('app', 'Export'),
                                    'aria-label' => Yii::t('app', 'Export'),
                                    'data' => [
                                        'method' => 'post',
                                        'params'=> ['name' => $model['name']],
                                    ]
                                ]);
                            },
                            'delete' => function ($url, $model, $key) {
                                $designPacksModule = Yii::$app->getModule(ModuleHelper::DESIGN_PACKS);
                                if ($designPacksModule->params['design_pack'] === $model['name']) {
                                    return '';
                                }
                                return Html::a(Html::icon('trash'), $url, [
                                    'title' => Yii::t('app', 'Delete'),
                                    'aria-label' => Yii::t('app', 'Delete'),
                                    'data' => [
                                        'method' => 'post',
                                        'confirm' => Yii::t('app', 'Are you sure?'),
                                        'params' => ['name' => $model['name']],
                                    ]
                                ]);
                            },
                        ],
                        'urlCreator' => function ($action, $model) {
                            if ($action === 'export') {
                                return Url::to([RouteHelper::ADMIN_DESIGN_PACKS_EXPORT, 'name' => $model['name']]);
                            } elseif ($action == 'edit') {
                                return Url::to([RouteHelper::ADMIN_DESIGN_PACKS_EDIT, 'name' => $model['name']]);
                            } elseif ($action == 'delete') {
                                return Url::to([RouteHelper::ADMIN_DESIGN_PACKS_DELETE, 'name' => $model['name']]);
                            }
                        },
                    ],
                ]
            ]) ?>
    <?= Html::endTag('div') ?>
<?= Html::endTag('div') ?>
