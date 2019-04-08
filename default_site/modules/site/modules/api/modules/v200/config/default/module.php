<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 18.04.2016 03:50
 * via Gii Module Generator
 */

return [
    'params' => $__params = require(__DIR__ . DIRECTORY_SEPARATOR . 'params.php'),
    'id' => 'v200',
    'aliases' => [

    ],
    'bootstrap' => [],
    //'defaultRoute' => 'default/index',
    'components' => [
        'users' => [
            'class' => 'api\modules\v200\components\Users',
        ],
        'roles' => [
            'class' => 'api\modules\v200\components\Roles',
        ],
        'services' => [
            'class' => 'api\modules\v200\components\Services',
        ],
        'plugins' => [
            'class' => 'api\modules\v200\components\Plugins',
        ],
    ],
    'modules' => [

    ]
];