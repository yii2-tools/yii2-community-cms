<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 09.04.16 14:57
 */

use app\helpers\Html;
use app\helpers\RouteHelper;

?>

<div class="row">
    <div class="col-xs-8">
        <?= $model->label ?>
    </div>

    <div class="col-xs-4">
        <?= Html::a(Html::icon('pencil'), [RouteHelper::ADMIN_DESIGN_MENU_ITEMS_UPDATE, 'id' => $model->id]) ?>
        <?= Html::a(Html::icon('trash'), [RouteHelper::ADMIN_DESIGN_MENU_ITEMS_DELETE, 'id' => $model->id], [
            'data' => [
                'method' => 'post',
                'confirm' => Yii::t('app', 'Are you sure?'),
            ],
        ]) ?>
    </div>
</div>
