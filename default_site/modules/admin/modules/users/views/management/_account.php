<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use app\helpers\ModuleHelper;

/**
 * @var yii\web\View $this
 * @var site\modules\users\models\User $model
 */

?>

<?php $this->beginContent('@admin/modules/users/views/management/update.php', ['model' => $model]) ?>

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'enableAjaxValidation'   => true,
        'enableClientValidation' => false,
        'fieldConfig' => [
            'horizontalCssClasses' => [
                'wrapper' => 'col-sm-9',
            ],
        ],
    ]); ?>
        <?= $this->render('_user', ['form' => $form, 'model' => $model]) ?>

        <?= Html::beginTag('div', ['class' => 'form-group']) ?>
            <?= Html::beginTag('div', ['class' => 'col-lg-offset-3 col-lg-9']) ?>
                <?= Html::submitButton(Yii::t('yii', 'Update'), ['class' => 'btn btn-block btn-success']) ?>
            <?= Html::endTag('div') ?>
        <?= Html::endTag('div') ?>
    <?php ActiveForm::end(); ?>

<?php $this->endContent();
