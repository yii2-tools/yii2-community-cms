<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 21.03.2016 16:49
 * via Gii Module Generator
 */

use app\helpers\ModuleHelper;
use yii\tools\params\models\ActiveParam;

return [
    'name' => Yii::t(ModuleHelper::PAGES, 'Pages'),
    'version' => '2.0.1',
    'pages_count' => [
        'class' => 'yii\tools\params\Number',
        'description' => Yii::t(ModuleHelper::PAGES, 'Pages count'),
        'flags' => ActiveParam::READ_ONLY,
    ],
];