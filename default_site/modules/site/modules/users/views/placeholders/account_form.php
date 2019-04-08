<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 01.04.16 15:43
 */

use yii\widgets\ActiveForm;
use app\helpers\Html;
use app\helpers\ModuleHelper;
use site\modules\users\models\SettingsForm;

$model = Yii::createObject(SettingsForm::className());

?>

<?php $form = ActiveForm::begin([
    'id'          => 'account-form',
    'options'     => ['class' => 'form-horizontal'],
    'fieldConfig' => [
        'template'     => "{label}\n<div class=\"col-lg-9\">{input}</div>\n<div class=\"col-sm-offset-3 col-lg-9\">{error}\n{hint}</div>",
        'labelOptions' => ['class' => 'col-lg-3 control-label'],
    ],
    'enableAjaxValidation'   => true,
    'enableClientValidation' => false,
]); ?>

    <?= $form->field($model, 'email') ?>

    <?= $form->field($model, 'new_password')->passwordInput() ?>

    <hr />

    <?= $form->field($model, 'current_password')->passwordInput() ?>

    <div class="form-group">
        <div class="col-lg-offset-3 col-lg-9">
            <?= Html::submitButton(Yii::t(ModuleHelper::USERS, 'Save'), ['class' => 'btn btn-block btn-success']) ?>
        </div>
    </div>

<?php ActiveForm::end(); ?>
