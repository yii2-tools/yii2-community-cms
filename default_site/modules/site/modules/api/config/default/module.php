<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 18.04.2016 03:50
 * via Gii Module Generator
 */

return [
    'params' => $__params = require(__DIR__ . DIRECTORY_SEPARATOR . 'params.php'),
    'id' => 'api',
    'aliases' => [
        '@api' => '@site/modules/api',
    ],
    'bootstrap' => [],
    //'defaultRoute' => 'default/index',
    'components' => [

    ],
    'modules' => [
        'v200' => [
            'class' => 'api\modules\v200\Module',
        ],
    ]
];