<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 02.05.16 14:37
 */

use yii\widgets\ActiveForm;
use dosamigos\ckeditor\CKEditor;
use app\helpers\RouteHelper;
use app\helpers\Html;
use site\modules\forum\models\PostForm;

$model = Yii::createObject(PostForm::className())->model;
$action = empty($model->id)
    ? [RouteHelper::SITE_FORUM_POSTS_CREATE, 'topicId' => $model->topic_id]
    : [RouteHelper::SITE_FORUM_POSTS_UPDATE, 'id' => $model->id];

?>

<?php $form = ActiveForm::begin([
    'id'                     => 'post-form',
    'action'                 => $action,
    'enableClientValidation' => false,
    'enableAjaxValidation'   => true,
    'validateOnChange'       => false,
    'validateOnBlur'         => false,
]) ?>

    <?= $form->field($model, 'topic_id', ['template' => '{input}', 'options' => []])->hiddenInput() ?>

    <?= $form->field($model, 'content')->label(false)->widget(
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

    <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary btn-block']) ?>

<?php ActiveForm::end(); ?>
