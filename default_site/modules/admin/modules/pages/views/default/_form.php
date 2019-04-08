<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 24.04.16 14:41
 *
 * @var Page $model
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\ckeditor\CKEditor;
use app\helpers\ModuleHelper;
use site\modules\pages\models\Page;

?>

<?php $form = ActiveForm::begin([
    'enableClientValidation' => false,
    'enableAjaxValidation'   => true,
]) ?>
    <?= $form->field($model, 'title') ?>

    <?= $form->field($model, 'content')->widget(
        CKEditor::className(),
        [
            'options' => [
                'style' => 'display: none',
            ],
            'clientOptions' => [
                'height' => 300,
                'language' => Yii::$app->language,
                'allowedContent' => true,
            ],
            'preset' => 'full',
        ]
    ) ?>

    <div class="alert-warning alert fade in">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <i class="icon fa fa-warning"></i>
        <?= Yii::t(ModuleHelper::ADMIN_PAGES, 'This form allows you to use script, iframe and other unsafe tags. Be aware what some programs (including your browser via custom plugins) may append some kind of dangerous code. Make sure that you have checked final code of your page after publishing') ?>.
    </div>

    <?= $model->renderRouteUrlField($form) ?>

    <?= $model->renderSecureAttributeField($form) ?>

    <?= $model->renderSecureAccessRolesField($form) ?>

    <?= Html::submitButton(Yii::t(ModuleHelper::ADMIN_USERS, 'Save'), ['class' => 'btn btn-success btn-block']) ?>
<?php ActiveForm::end(); ?>
