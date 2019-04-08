<?php

/**
 * @var $dataProvider array
 * @var $this         yii\web\View
 * @var $filterModel  admin\modules\users\models\Search
 */

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\ActionColumn;
use kartik\grid\GridView;
use app\helpers\ModuleHelper;
use app\helpers\RouteHelper;

$this->title = Yii::t(ModuleHelper::ADMIN_USERS, 'Permissions');
$this->params['breadcrumbs'][] = $this->title;
$columns = [
    [
        'attribute' => 'name',
        'label'     => Yii::t('app', 'Name'),
        'options'   => [
            'class' => 'col-md-5'
        ],
    ],
    [
        'attribute' => 'description',
        'label'    => Yii::t('app', 'Description'),
    ],
];

if (YII_ENV_DEV) {
    $columns = array_merge($columns, [
        [
            'attribute' => 'rule_name',
            'label'     => Yii::t(ModuleHelper::ADMIN_USERS, 'Rule name'),
            'options'   => [
                'style' => 'width: 15%'
            ],
        ],
        [
            'class'      => ActionColumn::className(),
            'template'   => '{update} {delete}',
            'urlCreator' => function ($action, $model) {
                    return Url::to([RouteHelper::ADMIN_USERS_PERMISSIONS_CONTROLLER . '/' . $action, 'name' => $model['name']]);
            },
            'options'   => [
                'style' => 'width: 5%'
            ]
        ],
    ]);
}
?>

<?= Html::beginTag('div', ['class' => 'row']) ?>
    <?= Html::beginTag('div', ['class' => 'col-md-12']) ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel'  => $filterModel,
            'layout'       => "{items}\n{pager}",
            'emptyText'    => Yii::t(ModuleHelper::ADMIN_USERS, 'No permissions available'),
            'pjax'         => true,
            'tableOptions' => ['class' => 'table table-striped'],
            'columns'      => $columns,
        ]) ?>
    <?= Html::endTag('div'); ?>
<?= Html::endTag('div');
