<?php

/**
 * @var $dataProvider array
 * @var $filterModel  admin\modules\users\models\Search
 * @var $this         yii\web\View
 */

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\ActionColumn;
use kartik\grid\GridView;
use app\helpers\ModuleHelper;
use app\helpers\RouteHelper;

$this->title = Yii::t(ModuleHelper::USERS, 'Roles');
$this->params['breadcrumbs'][] = $this->title;
$columns = [
    [
        'attribute' => 'name',
        'label'    => Yii::t('app', 'Name'),
        'options'   => [
            'class' => 'col-md-5'
        ],
    ],
    [
        'attribute' => 'description',
        'label'    => Yii::t(ModuleHelper::ADMIN_USERS, 'Description'),
    ],
];

if (YII_ENV_DEV) {
    $columns[] = [
        'attribute' => 'rule_name',
        'label'    => Yii::t(ModuleHelper::ADMIN_USERS, 'Rule name'),
        'options'   => [
            'style' => 'width: 15%'
        ],
    ];
}

$columns[] = [
    'class'      => ActionColumn::className(),
    'template'   => '{update} {delete}',
    'urlCreator' => function ($action, $model) {
            return Url::to([RouteHelper::ADMIN_USERS_ROLES_CONTROLLER . '/' . $action, 'name' => $model['name']]);
    },
    'options' => [
        'style' => 'width: 5%'
    ],
];

?>

<?= Html::beginTag('div', ['class' => 'row']) ?>
    <?= Html::beginTag('div', ['class' => 'col-md-12']) ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel'  => $filterModel,
            'layout'       => "{items}\n{pager}",
            'emptyText'    => Yii::t(ModuleHelper::ADMIN_USERS, 'No roles available'),
            'pjax'         => true,
            'tableOptions' => ['class' => 'table table-striped'],
            'columns'      => $columns,
        ]) ?>
    <?= Html::endTag('div'); ?>
<?= Html::endTag('div');
