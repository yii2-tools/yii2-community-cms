<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 21.03.2016 16:40
 * via Gii Module Generator
 */

return [
    'params' => $__params = require(__DIR__ . DIRECTORY_SEPARATOR . 'params.php'),
    'id' => 'forum',
    'aliases' => [

    ],
    'bootstrap' => [],
    'defaultRoute' => 'default/index',
    'components' => [
        'observer' => [
            'class' => 'admin\modules\forum\components\Observer',
        ],
    ],
    'modules' => [

    ]
];