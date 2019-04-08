<?php

use yii\helpers\Html;
?>

<?= Html::beginTag('div', ['class' => 'row']) ?>
    <?= Html::beginTag('div', ['class' => 'col-md-12']) ?>
        <?= $this->render('/auth_items/_form', ['model' => $model]) ?>
    <?= Html::endTag('div'); ?>
<?= Html::endTag('div'); ?>
