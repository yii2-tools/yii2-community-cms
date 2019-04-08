<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 29.04.16 14:05
 */

use app\helpers\ModuleHelper;
use admin\modules\users\helpers\RbacHelper;

return [
    [1, Yii::t(ModuleHelper::ADMIN_PAGES, 'About us'), 'about', Yii::t(ModuleHelper::ADMIN_PAGES, 'Some text here...'), 100000, 0, RbacHelper::PAGES_ACCESS_1, time(), time()],
];
