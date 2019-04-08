<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 26.02.16 11:59
 */

use yii\tools\params\models\ActiveParam;

return [
    'sidebar_collapse' => [
        'class' => 'yii\tools\params\Boolean',
        'flags' => ActiveParam::READ_ONLY | ActiveParam::COOKIE,
        'value' => true,
    ],
];