<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 21.03.2016 16:40
 * via Gii Module Generator
 */

use kartik\grid\GridView;
use app\helpers\ModuleHelper;
use app\helpers\Html;

$this->title = Yii::t(ModuleHelper::PAGES, 'Pages');
?>

<?= Html::beginTag('div', ['class' => 'row']) ?>
    <?= Html::beginTag('div', ['class' => 'col-md-12']) ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'layout' => "{items}\n{pager}",
            'emptyText' => Yii::t(ModuleHelper::ADMIN_PAGES, 'No pages created yet'),
            'pjax' => true,
            'options' => [
                'class' => 'table-pages',
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
                    'attribute' => 'route_id',
                    'noWrap'    => true,
                    'value' => function ($model) {
                        return $model->route->url_pattern;
                    },
                    'options'   => [
                        'class' => 'col-md-2'
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
                        'data-confirm' => Yii::t(ModuleHelper::ADMIN_PAGES,
                                'Are you sure you want to delete this page?')
                    ],
                ],
            ]
        ]) ?>
    <?= Html::endTag('div'); ?>
<?= Html::endTag('div'); ?>
