<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 06.05.16 19:35
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<?php $form = ActiveForm::begin([
    'enableClientValidation' => false,
    'enableAjaxValidation'   => true,
]) ?>

    <?= $form->field($model, 'title') ?>

    <?= $model->renderSecureAttributeField($form) ?>

    <?= $model->renderSecureAccessRolesField($form) ?>

    <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success btn-block']) ?>

<?php ActiveForm::end(); ?>
