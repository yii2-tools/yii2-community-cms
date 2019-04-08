<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 01.04.16 17:52
 */

use yii\widgets\ActiveForm;
use app\helpers\ModuleHelper;
use app\helpers\Html;
use site\modules\users\models\RecoveryForm;

$model = Yii::createObject([
    'class'    => RecoveryForm::className(),
    'scenario' => 'reset',
]);

?>

<?php $form = ActiveForm::begin([
    'id'                     => 'password-recovery-form',
    'enableAjaxValidation'   => true,
    'enableClientValidation' => false,
]); ?>

    <?= $form->field($model, 'password')->passwordInput() ?>

    <?= $form->field($model, 'password_repeat')->passwordInput() ?>

    <?= Html::submitButton(Yii::t(ModuleHelper::USERS, 'Finish'), ['class' => 'btn btn-success btn-block']) ?>

<?php ActiveForm::end(); ?>