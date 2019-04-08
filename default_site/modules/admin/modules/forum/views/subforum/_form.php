<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 06.05.16 19:35
 */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use site\modules\forum\Finder;

/** @var Finder $finder */
$finder = Yii::createObject(Finder::className());
$sections = ArrayHelper::map($finder->findSection([], false, true), 'id', 'title');

?>

<?php $form = ActiveForm::begin([
    'enableClientValidation' => false,
    'enableAjaxValidation'   => true,
]) ?>

    <?= $form->field($model, 'title') ?>

    <?= $form->field($model, 'description') ?>

    <?= $form->field($model, 'section_id')
        ->dropDownList($sections, [
            'prompt' => '-- ' . Yii::t('app', 'Choose from list') . ' --'
        ]
    ) ?>

    <?= $model->renderSecureAttributeField($form) ?>

    <?= $model->renderSecureAccessRolesField($form) ?>

    <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success btn-block']) ?>

<?php ActiveForm::end(); ?>
