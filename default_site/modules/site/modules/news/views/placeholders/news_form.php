<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 08.05.16 19:00
 */

use yii\widgets\ActiveForm;
use dosamigos\ckeditor\CKEditor;
use app\helpers\RouteHelper;
use app\helpers\Html;
use site\modules\news\models\NewsForm;

$model = Yii::$container->get(NewsForm::className())->model;
$action = empty($model->id)
    ? [RouteHelper::SITE_NEWS_CREATE]
    : [RouteHelper::SITE_NEWS_UPDATE, 'id' => $model->id];

?>

<?php $form = ActiveForm::begin([
    'id'                     => 'news-form',
    'action'                 => $action,
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
            ],
            'preset' => 'full',
        ]
    ) ?>

    <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary btn-block']) ?>

<?php ActiveForm::end(); ?>
