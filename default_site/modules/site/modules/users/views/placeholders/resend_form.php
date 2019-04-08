<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 01.04.16 17:24
 */

use yii\widgets\ActiveForm;
use yii\captcha\Captcha;
use app\helpers\Html;
use app\helpers\RouteHelper;
use app\helpers\ModuleHelper;
use site\modules\users\models\ResendForm;

$model = Yii::createObject(ResendForm::className());
$captcha_enabled = true;

?>

<?php $form = ActiveForm::begin([
    'id'                     => 'resend-form',
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