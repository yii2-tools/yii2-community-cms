<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Nav;
use app\helpers\RouteHelper;
use app\helpers\ModuleHelper;

/**
 * @var yii\web\View                $this
 * @var site\modules\users\models\User  $model
 */

$this->title = Yii::t(ModuleHelper::USERS, 'Create a user account');
$this->params['breadcrumbs'][] = ['label' => $this->title, 'options' => ['class' => 'active']];
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
                        ['label' => Yii::t(ModuleHelper::USERS, 'Account details'), 'url' => [RouteHelper::ADMIN_USERS_MANAGEMENT_CREATE]],
                        ['label' => Yii::t(ModuleHelper::USERS, 'Profile details'), 'options' => [
                            'class' => 'disabled',
                            'onclick' => 'return false;',
                        ]],
                        ['label' => Yii::t(ModuleHelper::USERS, 'Information'), 'options' => [
                            'class' => 'disabled',
                            'onclick' => 'return false;',
                        ]],
                    ],
                ]) ?>
            <?= Html::endTag('div') ?>
        <?= Html::endTag('div') ?>
    <?= Html::endTag('div') ?>

    <?= Html::beginTag('div', ['class' => 'col-md-9']) ?>
        <?= Html::beginTag('div', ['class' => 'alert alert-info']) ?>
            <?= Yii::t(ModuleHelper::USERS, 'Credentials will be sent to the user by email') ?>.
            <?= Yii::t(ModuleHelper::USERS, 'A password will be generated automatically if not provided') ?>.
        <?= Html::endTag('div') ?>
        <?php $form = ActiveForm::begin([
            'layout' => 'horizontal',
            'enableAjaxValidation'   => true,
            'enableClientValidation' => false,
            'fieldConfig' => [
                'horizontalCssClasses' => [
                    'wrapper' => 'col-sm-9',
                ],
            ],
        ]); ?>

        <?= $this->render('_user', ['form' => $form, 'model' => $model]) ?>

        <?= Html::beginTag('div', ['class' => 'form-group']) ?>
            <?= Html::beginTag('div', ['class' => 'col-lg-offset-3 col-lg-9']) ?>
                <?= Html::submitButton(Yii::t(ModuleHelper::USERS, 'Save'), ['class' => 'btn btn-block btn-success']) ?>
            <?= Html::endTag('div') ?>
        <?= Html::endTag('div') ?>

        <?php ActiveForm::end(); ?>
    <?= Html::endTag('div') ?>
<?= Html::endTag('div') ?>