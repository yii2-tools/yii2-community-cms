<?php

use yii\helpers\Html;
use yii\web\View;
use yii\bootstrap\Nav;
use app\helpers\ModuleHelper;
use app\helpers\RouteHelper;
use site\modules\users\models\User;

/**
 * @var View    $this
 * @var User    $model
 * @var string  $content
 */

$this->title = Yii::t(ModuleHelper::USERS, 'Update user account');
$this->params['breadcrumbs'][] = $this->title;
?>

<?= Html::beginTag('div', ['class' => 'row']) ?>
    <?= Html::beginTag('div', ['class' => 'col-md-3']) ?>
        <?= Html::beginTag('div', ['class' => 'box']) ?>
            <?= Html::beginTag('div', ['class' => 'box-body']) ?>
                <?= Nav::widget([
                    'options' => [
                        'class' => 'nav-pills nav-stacked',
                    ],
                    'items' => [
                        ['label' => Yii::t(ModuleHelper::USERS, 'Account details'), 'url' => [RouteHelper::ADMIN_USERS_MANAGEMENT_UPDATE, 'id' => $model->id]],
                        ['label' => Yii::t(ModuleHelper::USERS, 'Profile details'), 'url' => [RouteHelper::ADMIN_USERS_MANAGEMENT_UPDATE_PROFILE, 'id' => $model->id]],
                        ['label' => Yii::t(ModuleHelper::USERS, 'Information'), 'url' => [RouteHelper::ADMIN_USERS_MANAGEMENT_INFO, 'id' => $model->id]],
                        [
                            'label' => Yii::t(ModuleHelper::USERS, 'Assignments'),
                            'url' => [RouteHelper::ADMIN_USERS_MANAGEMENT_ASSIGNMENTS, 'id' => $model->id],
                            'visible' => true,
                        ],
                        '<hr>',
                        [
                            'label' => Yii::t(ModuleHelper::USERS, 'Confirm'),
                            'url'   => [RouteHelper::ADMIN_USERS_MANAGEMENT_CONFIRM, 'id' => $model->id],
                            'visible' => !$model->isConfirmed,
                            'linkOptions' => [
                                'class' => 'text-success',
                                'data-method' => 'post',
                                'data-confirm' => Yii::t(ModuleHelper::ADMIN_USERS, 'Are you sure you want to confirm this user?'),
                            ],
                        ],
                        [
                            'label' => Yii::t(ModuleHelper::USERS, 'Block'),
                            'url'   => [RouteHelper::ADMIN_USERS_MANAGEMENT_BLOCK, 'id' => $model->id],
                            'visible' => !$model->isBlocked,
                            'linkOptions' => [
                                'class' => 'text-danger',
                                'data-method' => 'post',
                                'data-confirm' => Yii::t(ModuleHelper::ADMIN_USERS, 'Are you sure you want to block this user?'),
                            ],
                        ],
                        [
                            'label' => Yii::t(ModuleHelper::USERS, 'Unblock'),
                            'url'   => [RouteHelper::ADMIN_USERS_MANAGEMENT_BLOCK, 'id' => $model->id],
                            'visible' => $model->isBlocked,
                            'linkOptions' => [
                                'class' => 'text-success',
                                'data-method' => 'post',
                                'data-confirm' => Yii::t(ModuleHelper::ADMIN_USERS, 'Are you sure you want to unblock this user?'),
                            ],
                        ],
                        [
                            'label' => Yii::t(ModuleHelper::USERS, 'Delete'),
                            'url'   => [RouteHelper::ADMIN_USERS_MANAGEMENT_DELETE, 'id' => $model->id],
                            'linkOptions' => [
                                'class' => 'text-danger',
                                'data-method' => 'post',
                                'data-confirm' => Yii::t(ModuleHelper::ADMIN_USERS, 'Are you sure you want to delete this user?'),
                            ],
                        ],
                    ],
                ]) ?>
            <?= Html::endTag('div'); ?>
        <?= Html::endTag('div'); ?>
    <?= Html::endTag('div'); ?>

    <?= Html::beginTag('div', ['class' => 'col-md-9']) ?>
        <?= $content ?>
    <?= Html::endTag('div'); ?>
<?= Html::endTag('div');
