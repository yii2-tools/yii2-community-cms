<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 08.05.2016 00:12
 * via Gii Module Generator
 */

use app\helpers\ModuleHelper;
use yii\tools\params\models\ActiveParam;

return [
    'name' => 'News',
    'version' => '2.0.1',
    'news_count' => [
        'class' => 'yii\tools\params\Number',
        'description' => Yii::t(ModuleHelper::NEWS, 'News count'),
        'flags' => ActiveParam::READ_ONLY,
    ],
];