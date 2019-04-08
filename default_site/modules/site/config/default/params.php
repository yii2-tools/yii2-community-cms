<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 23.01.16 16:04
 */

use app\helpers\ModuleHelper;
use yii\tools\params\models\ActiveParam;

return [
    'name' => 'Site',
    'version' => '2.0.1',
    'disk_space' => [
        'class' => 'yii\tools\params\Number',
        'flags' => ActiveParam::READ_ONLY,
        'description' => Yii::t(ModuleHelper::SITE, 'Disk space'),
    ],
];
