<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 06.05.16 19:37
 */

use app\helpers\RouteHelper;
use kartik\grid\GridView;
use app\helpers\ModuleHelper;
use app\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t(ModuleHelper::FORUM, 'Sections');

?>

<?= Html::beginTag('div', ['class' => 'row']) ?>
    <?= Html::beginTag('div', ['class' => 'col-md-12']) ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'layout' => "{items}\n{pager}",
            'emptyText' => Yii::t(ModuleHelper::ADMIN_FORUM, 'No sections created yet'),
            'pjax' => true,
            'options' => [
                'class' => 'table-sections',
            ],
            'columns' => [
                [
                    'attribute' => 'title',
                    'label'     => Yii::t('app', 'Name'),
                    'options'   => [
                        'class' => 'col-md-5'
                    ],
                ],
                [
                    'attribute' => 'created_at',
                    'hAlign'    => GridView::ALIGN_CENTER,
                    'noWrap'    => true,
                    'value' => function ($model) {
                        return Yii::$app->getFormatter()->asDatetime($model['created_at'], 'short');
                    },
                    'filterType' => GridView::FILTER_DATE,
                    'options'   => [
                        'class' => 'col-md-2'
                    ],
                ],
                [
                    'attribute' => 'updated_at',
                    'hAlign'    => GridView::ALIGN_CENTER,
                    'noWrap'    => true,
                    'value' => function ($model) {
                        return Yii::$app->getFormatter()->asDatetime($model['updated_at'], 'short');
                    },
                    'filterType' => GridView::FILTER_DATE,
                    'options'   => [
                        'class' => 'col-md-2'
                    ],
                ],
                [
                    'class' => 'kartik\grid\ActionColumn',
                    'template' => '{update} {delete}',
                    'deleteOptions' => [
                        'data-confirm' => Yii::t(ModuleHelper::ADMIN_FORUM,
                            'Are you sure you want to delete this section?')
                    ],
                    'urlCreator' => function ($action, $model) {
                        if ($action == 'update') {
                            return Url::to([RouteHelper::ADMIN_FORUM_SECTIONS_UPDATE, 'id' => $model['id']]);
                        } elseif ($action == 'delete') {
                            return Url::to([RouteHelper::ADMIN_FORUM_SECTIONS_DELETE, 'id' => $model['id']]);
                        }
                    },
                ],
            ]
        ]) ?>
    <?= Html::endTag('div'); ?>
<?= Html::endTag('div'); ?>
