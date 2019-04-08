<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 06.05.16 19:36
 */

use kartik\grid\GridView;
use app\helpers\ModuleHelper;
use app\helpers\Html;

$this->title = Yii::t(ModuleHelper::FORUM, 'Subforums');

?>

<?= Html::beginTag('div', ['class' => 'row']) ?>
    <?= Html::beginTag('div', ['class' => 'col-md-12']) ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'layout' => "{items}\n{pager}",
            'emptyText' => Yii::t(ModuleHelper::ADMIN_FORUM, 'No subforums created yet'),
            'pjax' => true,
            'options' => [
                'class' => 'table-sections',
            ],
            'columns' => [
                [
                    'attribute' => 'title',
                    'label'     => Yii::t('app', 'Name'),
                    'options'   => [
                        'class' => 'col-md-3'
                    ],
                ],
                [
                    'attribute' => 'section_id',
                    'label'     => Yii::t(ModuleHelper::FORUM, 'Section'),
                    'value' => function ($model) {
                        return $model->section->title;
                    },
                    'options'   => [
                        'class' => 'col-md-3'
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
                            'Are you sure you want to delete this subforum?')
                    ],
                ],
            ]
        ]) ?>
    <?= Html::endTag('div'); ?>
<?= Html::endTag('div'); ?>
