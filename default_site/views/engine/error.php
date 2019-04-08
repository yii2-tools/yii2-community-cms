<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = Yii::t('errors', $name);

$code = $exception instanceof \yii\web\HttpException ? $exception->statusCode : $exception->getCode();

?>
<div class="panel">
    <div class="panel-body">
        <h1><?= Html::encode($this->title) ?></h1>

        <? if (!empty($message)) : ?>
        <div class="alert alert-danger">
            <?= nl2br(Html::encode($message)) ?>
        </div>
        <? endif ?>

        <?php if (!preg_match('/4\d{2}/', $code)): ?>
        <p>
            <?= Yii::t('errors', 'Please contact us if you think this is a server error: {0}. Thank you.', Yii::$app->params['admin_email']) ?>
        </p>
        <?php endif ?>
    </div>
</div>
