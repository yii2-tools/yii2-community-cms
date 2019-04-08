<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 02.05.16 14:37
 */

use yii\widgets\ActiveForm;
use dosamigos\ckeditor\CKEditor;
use app\helpers\RouteHelper;
use app\helpers\Html;
use site\modules\forum\models\TopicForm;

$model = Yii::$container->get(TopicForm::className())->model;
$action = empty($model->id)
    ? [RouteHelper::SITE_FORUM_TOPICS_CREATE, 'subforum_id' => $model->subforum_id]
    : [RouteHelper::SITE_FORUM_TOPICS_UPDATE, 'id' => $model->id];

?>

<?php $form = ActiveForm::begin([
    'id'                     => 'topic-form',
    'action'                 => $action,
    'enableClientValidation' => false,
    'enableAjaxValidation'   => true,
]) ?>

    <?= $form->field($model, 'subforum_id', ['template' => '{input}', 'options' => []])->hiddenInput() ?>

    <?= $form->field($model, 'title') ?>

    <?= $form->field($model, 'description') ?>

    <?= $form->field($model, 'content')->widget(
        CKEditor::className(),
        [
            'options' => [
                'style' => 'display: none',
            ],
            'clientOptions' => [
                'height' => 200,
                'language' => Yii::$app->language,
            ],
            'preset' => 'full',
        ]
    ) ?>

    <?= $form->field($model, 'is_closed')->checkbox() ?>

    <?= $form->field($model, 'is_fixed')->checkbox() ?>

    <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary btn-block']) ?>

<?php ActiveForm::end(); ?>
