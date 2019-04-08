<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 01.04.16 16:55
 */

use yii\widgets\ActiveForm;
use yii\captcha\Captcha;
use app\helpers\RouteHelper;
use app\helpers\Html;
use app\helpers\ModuleHelper;
use site\modules\users\models\RegistrationForm;

$model = Yii::createObject(RegistrationForm::className());
$module = Yii::$app->getModule(ModuleHelper::USERS);
$captcha_enabled = true;

?>

<?php $form = ActiveForm::begin([
    'id'                     => 'registration-form',
    'enableAjaxValidation'   => true,
    'enableClientValidation' => false,
]); ?>

    <?= $form->field($model, 'username') ?>
    <?= $form->field($model, 'email') ?>

    <?php if ($module->enableGeneratingPassword == false) : ?>
        <?= $form->field($model, 'password')->passwordInput() ?>
        <?= $form->field($model, 'password_repeat')->passwordInput() ?>
    <?php endif ?>

    <? if ($captcha_enabled) : ?>
        <?= $form->field($model, 'captcha')->widget(Captcha::classname(), [
                'captchaAction' => [RouteHelper::SITE_CAPTCHA]
            ]) ?>
    <? endif ?>

    <?= Html::submitButton(Yii::t(ModuleHelper::USERS, 'Sign up'), ['class' => 'btn btn-success btn-block']) ?>

<?php ActiveForm::end(); ?>