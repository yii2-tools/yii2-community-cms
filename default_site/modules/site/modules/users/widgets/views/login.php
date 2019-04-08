<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use app\helpers\ModuleHelper;
use app\helpers\RouteHelper;

/*
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var site\modules\users\models\LoginForm $model
 * @var string $action
 */
?>

<?php if (Yii::$app->user->isGuest) : ?>

    <?php $form = ActiveForm::begin([
        'id' => 'login-widget-form',
        'fieldConfig' => [
            'template' => "{input}\n{error}",
        ],
        'action' => $action,
    ]) ?>

    <?= $form->field($model, 'login')->textInput(['placeholder' => 'Login']) ?>

    <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Password']) ?>

    <?= $form->field($model, 'rememberMe')->checkbox() ?>

    <?= Html::submitButton(Yii::t(ModuleHelper::USERS, 'Sign in'), ['class' => 'btn btn-primary btn-block']) ?>

    <?php ActiveForm::end(); ?>

<?php else : ?>

    <?= Html::a(Yii::t(ModuleHelper::USERS, 'Logout'), [RouteHelper::SITE_USERS_LOGOUT], ['class' => 'btn btn-danger btn-block', 'data-method' => 'post']) ?>

<?php endif;
