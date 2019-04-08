<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 29.04.16 14:05
 */

use app\helpers\ModuleHelper;
use admin\modules\users\helpers\RbacHelper;

return [
    [1, Yii::t(ModuleHelper::ADMIN_NEWS, 'Welcome') . '!', 'welcome', '<p>' . Yii::t(ModuleHelper::ADMIN_NEWS, 'Welcome to your new team site') . '!</p>', 0, RbacHelper::NEWS_ACCESS_1, 1, time(), time()],
];
