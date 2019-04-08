<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 04.04.16 9:08
 */

use app\helpers\Html;
use app\helpers\RouteHelper;

?>

<div class="row">
    <div class="col-xs-12">
        <strong><?= $model['title'] ?></strong>
    </div>

    <div class="col-xs-12">
        <?= Html::a(
            Html::faIcon('file-archive-o') . ' ' . $model['name'],
            [RouteHelper::ADMIN_DESIGN_PACKS_EXPORT, 'name' => $model['name']],
            [
                'title' => Yii::t('app', 'Export'),
                'aria-label' => Yii::t('app', 'Export'),
                'data' => [
                    'method' => 'post',
                    'params'=> ['name' => $model['name']],
                ]
            ]
        ) ?>
    </div>

    <div class="col-xs-12 margin-top-10">
        <?= Yii::t('app', 'Updated') ?> <?= Yii::$app->getFormatter()->asDatetime($model['updated_at'], 'short') ?>
    </div>
</div>
