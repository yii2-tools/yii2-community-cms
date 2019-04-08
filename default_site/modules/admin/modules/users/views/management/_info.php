<?php

/**
 * @var yii\web\View
 * @var site\modules\users\models\User
 */

use app\helpers\ModuleHelper;

?>

<?php $this->beginContent('@admin/modules/users/views/management/update.php', ['model' => $model]) ?>

<table class="table table-borderless no-margin">
    <tr>
        <td class="col-xs-6"><strong><?= Yii::t(ModuleHelper::USERS, 'Registration date') ?>:</strong></td>
        <td class="col-xs-6"><?= Yii::t(ModuleHelper::USERS, '{0, date, MMMM dd, YYYY HH:mm}', [$model->created_at]) ?></td>
    </tr>
    <?php if ($model->registration_ip !== null) : ?>
        <tr>
            <td><strong><?= Yii::t(ModuleHelper::USERS, 'Registration IP') ?>:</strong></td>
            <td><?= $model->registration_ip ?></td>
        </tr>
    <?php endif ?>
    <tr>
        <td><strong><?= Yii::t(ModuleHelper::USERS, 'Confirmation status') ?>:</strong></td>
        <?php if ($model->isConfirmed) : ?>
            <td class="text-success"><?= Yii::t(ModuleHelper::USERS, 'Confirmed at {0, date, MMMM dd, YYYY HH:mm}', [$model->confirmed_at]) ?></td>
        <?php else : ?>
            <td class="text-danger"><?= Yii::t(ModuleHelper::USERS, 'Unconfirmed') ?></td>
        <?php endif ?>
    </tr>
    <tr>
        <td><strong><?= Yii::t(ModuleHelper::USERS, 'Block status') ?>:</strong></td>
        <?php if ($model->isBlocked) : ?>
            <td class="text-danger"><?= Yii::t(ModuleHelper::USERS, 'Blocked at {0, date, MMMM dd, YYYY HH:mm}', [$model->blocked_at]) ?></td>
        <?php else : ?>
            <td class="text-success"><?= Yii::t(ModuleHelper::USERS, 'Not blocked') ?></td>
        <?php endif ?>
    </tr>
</table>

<?php $this->endContent() ?>
