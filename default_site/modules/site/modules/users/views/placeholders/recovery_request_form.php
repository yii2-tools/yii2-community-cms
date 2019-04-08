<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 01.04.16 17:44
 */

use yii\captcha\Captcha;
use yii\widgets\ActiveForm;
use app\helpers\Html;
use app\helpers\ModuleHelper;
use app\helpers\RouteHelper;
use site\modules\users\models\RecoveryForm;

$model = Yii::createObject([
    'class'    => RecoveryForm::className(),
    'scenario' => 'request',
]);
$captcha_enabled = true;

?>

<?php $form = ActiveForm::begin([
    'id'                     => 'password-recovery-form',
    'enableAjaxValidation'   => true,
    'enableClientValidation' => false,
]); ?>

    <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>

    <? if ($captcha_enabled) : ?>
        <?= $form->field($model, 'captcha')->widget(Captcha::classname(), [
            'captchaAction' => [RouteHelper::SITE_CAPTCHA]
        ]) ?>
    <? endif ?>

    <?= Html::submitButton(Yii::t(ModuleHelper::USERS, 'Continue'), ['class' => 'btn btn-primary btn-block']) ?>

<?php ActiveForm::end(); ?>
