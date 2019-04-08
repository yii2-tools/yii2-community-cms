<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 01.04.16 12:42
 */

use yii\widgets\Menu;
use app\helpers\ModuleHelper;
use app\helpers\RouteHelper;

?>

<?= Menu::widget([
    'options' => [
        'class' => 'nav nav-pills nav-stacked',
    ],
    'items' => [
        ['label' => Yii::t(ModuleHelper::USERS, 'Profile'), 'url' => [RouteHelper::SITE_USERS_SETTINGS_PROFILE]],
        ['label' => Yii::t(ModuleHelper::USERS, 'Private settings'), 'url' => [RouteHelper::SITE_USERS_SETTINGS_ACCOUNT]],
    ],
]) ?>