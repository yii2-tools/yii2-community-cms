<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 21.03.2016 16:39
 * via Gii Module Generator
 */

return [
    'params' => $__params = require(__DIR__ . DIRECTORY_SEPARATOR . 'params.php'),
    'id' => 'design',
    'aliases' => [

    ],
    'bootstrap' => [],
    'defaultRoute' => 'default/index',
    'components' => [

    ],
    'modules' => [
        'packs' => [
            'class' => 'admin\modules\design\modules\packs\Module',
        ],
        'menu' => [
            'class' => 'admin\modules\design\modules\menu\Module',
        ],
    ]
];