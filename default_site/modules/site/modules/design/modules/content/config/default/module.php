<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 27.03.2016 19:04
 * via Gii Module Generator
 */

return [
    'params' => $__params = require(__DIR__ . DIRECTORY_SEPARATOR . 'params.php'),
    'id' => 'content',
    'aliases' => [

    ],
    'bootstrap' => [],
    'defaultRoute' => 'default/index',
    'components' => [
        'resolver' => [
            'class' => 'design\modules\content\components\Resolver',
            'pattern' => '\s+([A-Z_]+)(?:\s*\|\s*[a-z]+)?\s+',
            'tags' => ['{{', '}}'],
        ],
        // per request cache for placeholders
        'cache' => [
            'class' => 'yii\caching\ArrayCache',
        ]
    ],
    'modules' => [

    ]
];