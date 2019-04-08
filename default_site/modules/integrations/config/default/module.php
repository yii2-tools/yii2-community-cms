<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 20.02.2016 14:35
 * via Gii Module Generator
 */

return [
    'params' => $__params = require(__DIR__ . DIRECTORY_SEPARATOR . 'params.php'),
    'aliases' => [

    ],
    'id' => 'integrations',
    'bootstrap' => [],
    'defaultRoute' => 'default/index',
    'components' => [
        'placeholders' => [
            'class' => 'app\modules\integrations\components\PlaceholderIntegrator'
        ]
    ],
    'modules' => [
        'companyName' => [
            'class' => 'integrations\modules\companyName\Module'
        ]
    ]
];