<?php

/**
 * @var $this  yii\web\View
 * @var $model admin\modules\users\models\AuthItemForm
 */

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use kartik\select2\Select2;
use app\helpers\ModuleHelper;

?>

<?php $form = ActiveForm::begin([
    'enableClientValidation' => false,
    'enableAjaxValidation'   => true,
]) ?>
    <?= $form->field($model, 'name')->label(Yii::t('app', 'Name')) ?>
    <?= $form->field($model, 'description') ?>

    <?php if (YII_ENV_DEV) : ?>
        <?= $form->field($model, 'rule')->label(Yii::t(ModuleHelper::ADMIN_USERS, 'Rule name')) ?>
    <?php endif ?>

    <?= $form->field($model, 'children')->widget(Select2::className(), [
        'data' => $model->getUnassignedItems(),
        'options' => [
            'id' => 'children',
            'multiple' => true,
        ],
    ])->label(Yii::t(ModuleHelper::ADMIN_USERS, 'Nested roles and permissions')) ?>

    <?= Html::submitButton(Yii::t(ModuleHelper::ADMIN_USERS, 'Save'), ['class' => 'btn btn-success btn-block']) ?>
<?php ActiveForm::end(); ?>
