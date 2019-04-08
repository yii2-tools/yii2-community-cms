<?php

use yii\bootstrap\Html;
use yii\web\View;
use yii\data\ActiveDataProvider;
use kartik\grid\GridView;
use app\helpers\ModuleHelper;
use admin\modules\users\models\UserSearch;
use site\modules\users\assets\UsersAsset;

/**
 * @var View $this
 * @var ActiveDataProvider $dataProvider
 * @var UserSearch $searchModel
 */

$this->title = Yii::t(ModuleHelper::ADMIN_USERS, 'Users management');
unset($this->params['breadcrumbs']['module']['url']);

UsersAsset::register($this);

?>

<?= Html::beginTag('div', ['class' => 'row']) ?>
    <?= Html::beginTag('div', ['class' => 'col-md-12']) ?>
        <?= GridView::widget([
            'dataProvider'  => $dataProvider,
            'filterModel'   => $searchModel,
            'layout'        => "{items}\n{pager}",
            'emptyText'     => 'Нет пользователей для отображения',
            'pjax'          => true,
            'tableOptions'  => ['class' => 'table table-striped'],
            'columns' => [
                'username',
                'email:email',
                [
                    'attribute' => 'registration_ip',
                    'value' => function ($model) {
                        return $model->registration_ip == null
                            ? '<span class="not-set">' . Yii::t(ModuleHelper::USERS, '(not set)') . '</span>'
                            : $model->registration_ip;
                    },
                    'format' => 'html',
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
                    'attribute' => 'confirmed_at',
                    'label' => Yii::t(ModuleHelper::USERS, 'Confirmation'),
                    'value' => function ($model) {
                        if ($model->isConfirmed) {
                            return '<div class="text-center"><span class="text-success">' . Yii::$app->getFormatter()->asBoolean($model->isConfirmed) . '</span></div>';
                        } else {
                            return Html::a(Yii::t(ModuleHelper::USERS, 'Confirm'), ['confirm', 'id' => $model->id], [
                                'class' => 'btn btn-xs btn-success btn-block',
                                'data-method' => 'post',
                                'data-confirm' => Yii::t(ModuleHelper::ADMIN_USERS, 'Are you sure you want to confirm this user?'),
                            ]);
                        }
                    },
                    'format' => 'raw',
                    'filter' => [
                        1 => Yii::t(ModuleHelper::USERS, 'Confirmed'),
                        2 => Yii::t(ModuleHelper::USERS, 'Not confirmed'),
                    ],
                    'visible' => Yii::$app->getModule(ModuleHelper::USERS)->enableConfirmation,
                ],
                [
                    'attribute' => 'blocked_at',
                    'label' => Yii::t(ModuleHelper::USERS, 'Block status'),
                    'value' => function ($model) {
                        if ($model->isBlocked) {
                            return Html::a(Yii::t(ModuleHelper::USERS, 'Unblock'), ['block', 'id' => $model->id], [
                                'class' => 'btn btn-xs btn-success btn-block',
                                'data-method' => 'post',
                                'data-confirm' => Yii::t(ModuleHelper::ADMIN_USERS, 'Are you sure you want to unblock this user?'),
                            ]);
                        } else {
                            return Html::a(Yii::t(ModuleHelper::USERS, 'Block'), ['block', 'id' => $model->id], [
                                'class' => 'btn btn-xs btn-danger btn-block',
                                'data-method' => 'post',
                                'data-confirm' => Yii::t(ModuleHelper::ADMIN_USERS, 'Are you sure you want to block this user?'),
                            ]);
                        }
                    },
                    'format' => 'raw',
                    'filter' => [
                        1 => Yii::t(ModuleHelper::USERS, 'Blocked'),
                        2 => Yii::t(ModuleHelper::USERS, 'Not blocked'),
                    ],
                ],
                [
                    'class' => 'kartik\grid\ActionColumn',
                    'template' => '{update} {delete}',
                    'deleteOptions' => [
                        'data-confirm' => Yii::t(ModuleHelper::ADMIN_USERS, 'Are you sure you want to delete this user?')
                    ],
                ],
            ],
        ]); ?>
    <?= Html::endTag('div'); ?>
<?= Html::endTag('div');
