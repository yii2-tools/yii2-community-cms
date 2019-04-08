<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 16.04.2016 14:50
 * via Gii Module Generator
 */

return [
    'params' => $__params = require(__DIR__ . DIRECTORY_SEPARATOR . 'params.php'),
    'id' => 'services',
    'aliases' => [

    ],
    'bootstrap' => [],
    //'defaultRoute' => 'default/index',
    'components' => [
        'manager' => [
            'class' => 'app\modules\services\components\Manager',
        ],
    ],
    'modules' => [

    ]
];