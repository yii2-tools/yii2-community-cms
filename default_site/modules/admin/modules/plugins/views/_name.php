<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 18.05.16 10:25
 */

use yii\helpers\Url;
use app\helpers\Html;
use app\helpers\RouteHelper;

?>

<div class="row">
    <div class="col-xs-12">
        <strong><?= $model['title'] ?></strong>
    </div>

    <?php if (isset($model['status']) && 0 < intval($model['status'])): ?>
    <div class="col-xs-12">
        <span class="label label-default">
            <?= Html::a(
                Url::to([RouteHelper::SITE_PLUGINS_SHOW, 'name' => $model['plugin_dir_name']]),
                [RouteHelper::SITE_PLUGINS_SHOW, 'name' => $model['plugin_dir_name']],
                [
                    'data-pjax' => "0",
                ]
            ) ?>
        </span>
    </div>
    <?php endif ?>
</div>
