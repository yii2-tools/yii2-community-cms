<?php

use admin\modules\users\widgets\Assignments;
use app\helpers\ModuleHelper;

/**
 * @var yii\web\View                $this
 * @var site\modules\users\models\User  $model
 */

?>

<?php $this->beginContent('@admin/modules/users/views/management/update.php', ['model' => $model]) ?>

<?= yii\bootstrap\Alert::widget([
    'options' => [
        'class' => 'alert-info',
    ],
    'body' => Yii::t(ModuleHelper::USERS, 'You can assign multiple roles or permissions to user by using the form below'),
]) ?>

<?= Assignments::widget(['userId' => $model->id]) ?>

<?php $this->endContent();
