<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 01.04.16 11:49
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\fileupload\FileUpload;
use app\helpers\ModuleHelper;
use app\models\UploadForm;

$finder = Yii::$container->get('site\modules\users\Finder');
$model = $finder->findProfileById(Yii::$app->getUser()->getIdentity()->getId());
$uploadForm = Yii::createObject(UploadForm::className());

?>

<?php $form = ActiveForm::begin([
    'id' => 'profile-form',
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
        'template' => "{label}\n<div class=\"col-lg-9\">{input}</div>\n<div class=\"col-sm-offset-3 col-lg-9\">{error}\n{hint}</div>",
        'labelOptions' => ['class' => 'col-lg-3 control-label'],
    ],
    'enableAjaxValidation'   => true,
    'enableClientValidation' => false,
    'validateOnBlur'         => false,
]); ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'location') ?>

    <?= $form->field($uploadForm, 'imageFile')
        ->label(Yii::t(ModuleHelper::USERS, 'Avatar'))
        ->widget(FileUpload::className(), [
            'model' => $uploadForm,
            'attribute' => 'imageFile',
            'url' => ['settings/profile-image-upload', 'id' => $model->user_id],
            'options' => ['accept' => 'image/*'],
            'clientOptions' => [
                'maxFileSize' => 2000000
            ],
            'clientEvents' => [
                'fileuploaddone' => 'function(e, data) {
                    if ("undefined" !== typeof data.result.errors.imageFile) {
                        $("#profile-form").yiiActiveForm("updateMessages", {
                            "uploadform-imagefile": data.result.errors.imageFile
                        }, true);
                    } else if ("undefined" !== typeof data.result.data.image_url) {
                        //var profileImageElement = $("#profile-image");
                        //profileImageElement.attr("src", data.result.data.image_url).show();
                        //profileImageElement.removeClass("hidden");
                        location.reload();
                    }
                }',
            ],
    ]) ?>

    <div class="form-group">
        <div class="col-lg-offset-3 col-lg-9">
            <?= Html::submitButton(Yii::t(ModuleHelper::USERS, 'Save'),
                ['class' => 'btn btn-block btn-success']) ?>
        </div>
    </div>

<?php ActiveForm::end(); ?>
