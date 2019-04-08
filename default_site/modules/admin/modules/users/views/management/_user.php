<?php

/**
 * @var yii\widgets\ActiveForm      $form
 * @var site\modules\users\models\User  $model
 */
?>

<?= $form->field($model, 'email')->textInput(['maxlength' => 255]) ?>
<?= $form->field($model, 'username')->textInput(['maxlength' => 255]) ?>
<?= $form->field($model, 'password')->passwordInput();
