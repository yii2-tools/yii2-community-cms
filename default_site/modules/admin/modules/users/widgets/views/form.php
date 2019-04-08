<?php

use kartik\select2\Select2;
use yii\bootstrap\Alert;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\helpers\ModuleHelper;
use admin\modules\users\models\Assignment;

/**
 * @var $model Assignment
 */

?>

<?php if ($model->updated) : ?>

<?= Alert::widget([
    'options' => [
        'class' => 'alert-success'
    ],
    'body' => Yii::t(ModuleHelper::ADMIN_USERS, 'Assignments have been updated'),
]) ?>

<?php endif ?>

<?php $form = ActiveForm::begin([
    'enableClientValidation' => false,
    'enableAjaxValidation'   => false,
]) ?>

<?= Html::activeHiddenInput($model, 'user_id') ?>

<?= $form->field($model, 'items')->widget(Select2::className(), [
    'data' => $model->getAvailableItems(),
    'options' => [
        'id' => 'items',
        'multiple' => true
    ],
]) ?>

<?= Html::submitButton(Yii::t(ModuleHelper::ADMIN_USERS, 'Update assignments'), ['class' => 'btn btn-success btn-block']) ?>

<?php ActiveForm::end();

