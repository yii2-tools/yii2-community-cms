<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 10.04.16 13:19
 *
 * @var \yii\web\View $this
 */

use yii\widgets\ActiveForm;
use app\helpers\Html;

$js = <<<JAVASCRIPT
    $(document).on('change', '#itemform-is_route', function (event) {
        var routeIdField = $('#itemform-route_id');
        var urlField = $('#itemform-url');
        routeIdField.prop('disabled', !routeIdField.prop('disabled'));
        urlField.prop('disabled', !urlField.prop('disabled'));
    });
JAVASCRIPT;

$this->registerJs($js);

?>

<?= Html::beginTag('div', ['class' => 'row']) ?>
    <?= Html::beginTag('div', ['class' => 'col-md-12']) ?>
        <?php $form = ActiveForm::begin([
            'enableClientValidation' => false,
            'enableAjaxValidation'   => true,
        ]) ?>

            <?= $form->field($model, 'label') ?>

            <?= $form->field($model, 'url', [
                'inputOptions' => [
                    'class' => 'form-control',
                    'disabled' => !!$model->is_route
                ],
            ]) ?>

            <?= $form->field($model, 'is_route')->checkbox() ?>

            <?= $form->field($model, 'route_id')
                ->dropDownList($model->getRoutesList(), [
                    'prompt' => '-- ' . Yii::t('app', 'Choose from list') . ' --',
                    'disabled' => !!!$model->is_route,
            ]) ?>

            <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success btn-block']) ?>

        <?php ActiveForm::end(); ?>
    <?= Html::endTag('div'); ?>
<?= Html::endTag('div'); ?>
