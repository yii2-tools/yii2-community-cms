<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 31.03.16 10:03
 */

use yii\widgets\ActiveForm;
use yii\captcha\Captcha;
use app\helpers\ModuleHelper;
use app\helpers\RouteHelper;
use app\helpers\SessionHelper;
use app\helpers\Html;
use site\modules\users\models\LoginForm;

$model = Yii::createObject(LoginForm::className());
$model->login = Yii::$app->getSession()->get(SessionHelper::AUTH_LOGIN, '');
$module = Yii::$app->getModule(ModuleHelper::USERS);
$isCaptchaEnabled = Yii::$app->getModule(ModuleHelper::SITE)->captcha->isRequired()

?>

<?php $form = ActiveForm::begin([
    'id'                     => 'login-form',
    'action'                 => [RouteHelper::SITE_USERS_LOGIN],
    'enableAjaxValidation'   => true,
    'enableClientValidation' => false,
    'validateOnBlur'         => false,
    'validateOnType'         => false,
    'validateOnChange'       => false,
]) ?>

    <?= $form->field($model, 'login', ['inputOptions' => ['autofocus' => 'autofocus', 'class' => 'form-control', 'tabindex' => '1']]) ?>

    <?= $form->field($model, 'password', ['inputOptions' => ['class' => 'form-control', 'tabindex' => '2']])->passwordInput()->label(Yii::t(ModuleHelper::USERS, 'Password') . ($module->enablePasswordRecovery ? ' (' . Html::a(Yii::t(ModuleHelper::USERS, 'Forgot password?'), [RouteHelper::SITE_USERS_RECOVERY_REQUEST], ['tabindex' => '5']) . ')' : '')) ?>

    <?= $form->field($model, 'rememberMe')->checkbox(['tabindex' => '4']) ?>

    <? if ($isCaptchaEnabled) : ?>
        <?= $form->field($model, 'captcha')->widget(Captcha::classname(), [
            'captchaAction' => [RouteHelper::SITE_CAPTCHA]
        ]) ?>
    <? endif ?>

    <?= Html::submitButton(Yii::t(ModuleHelper::USERS, 'Sign in'), ['class' => 'btn btn-primary btn-block', 'tabindex' => '3']) ?>

<?php ActiveForm::end(); ?>
