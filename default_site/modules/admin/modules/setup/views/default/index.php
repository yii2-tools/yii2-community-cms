<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 08.02.2016 14:19
 * via Gii Module Generator
 *
 * @var $module \app\components\Module
 * @var $models \yii\base\Model[]
 */

use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;
use app\helpers\ModuleHelper;
use app\helpers\RouteHelper;
use yii\tools\helpers\FormatHelper;
use admin\modules\setup\assets\SetupAsset;

$this->title = Yii::t(ModuleHelper::ADMIN_SETUP, 'Setup');
$this->params['breadcrumbs'][] = $module->getBreadcrumbs();
$this->params['breadcrumbs'][] = $this->title;

SetupAsset::register($this);
?>

<?= Html::beginTag('div', ['class' => 'row']) ?>
    <?= Html::beginTag('div', ['class' => 'col-md-12 admin-setup']) ?>
        <?php if (!empty($models)) : ?>
        <?php $form = ActiveForm::begin([
            'id'                     => 'setup-form',
            'action'                 => [RouteHelper::ADMIN_SETUP_UPDATE, 'module' => $module->id],
            'enableAjaxValidation'   => true,
            'enableClientValidation' => false,
            'validateOnType'         => false,
            'validateOnChange'       => false,
        ]); ?>
            <?php foreach ($models as $index => $model) : ?>
                <?php
                    $fieldName = $model->type == FormatHelper::TYPE_LIST
                        ? $model->listValueAttribute()
                        : 'value';
                    $field = $form->field($model, "[$index]" . $fieldName);
                if ($model->type == FormatHelper::TYPE_DATE) {
                    $field->widget(DatePicker::className(), [
                        'dateFormat' => 'php:d.m.Y',
                        'options' => [
                            'class' => 'form-control'
                        ],
                    ]);
                } elseif ($model->type == FormatHelper::TYPE_LIST) {
                    $field = $field->dropDownList($model->getListValuesArray(), [
                        'prompt' => '-- ' . Yii::t('app', 'Choose from list') . ' --',
                    ]);
                } elseif ($model->type == FormatHelper::TYPE_NUMBER) {
                    $field = $field->textInput([
                        'type' => 'number'
                    ]);
                } elseif ($model->type == FormatHelper::TYPE_BOOLEAN) {
                    $field->template = "{input} &nbsp; {label}\n{hint}\n{error}";
                    $field = $field->checkbox([], false);
                }
                ?>
                <?= $field->label($model->description) ?>
            <?php endforeach ?>
            <?= Html::beginTag('div', ['class' => 'form-group']) ?>
                <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary btn-block']) ?>
            <?= Html::endTag('div') ?>
        <?php ActiveForm::end(); ?>
        <?php else : ?>
        <?= Html::beginTag('div', ['class' => 'text-center']) ?>
            <?php echo Yii::t(ModuleHelper::ADMIN_SETUP, 'No params available'); ?>
        <?= Html::endTag('div') ?>
        <?php endif ?>
    <?= Html::endTag('div') ?>
<?= Html::endTag('div');
