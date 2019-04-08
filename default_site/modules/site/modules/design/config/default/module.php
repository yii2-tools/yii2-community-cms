<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 13.03.2016 08:01
 * via Gii Module Generator
 */

return [
    'params' => $__params = require(__DIR__ . DIRECTORY_SEPARATOR . 'params.php'),
    'id' => 'design',
    'aliases' => [
        '@design' => '@site/modules/design',
    ],
    'bootstrap' => ['content', 'packs', 'menu'],
    'defaultRoute' => 'default/index',
    'components' => [
        'renderer' => require(__DIR__ . DIRECTORY_SEPARATOR . 'template_engine.php')
    ],
    'modules' => [
        'content' => [
            'class' => 'design\modules\content\Module',
        ],
        'packs' => [
            'class' => 'design\modules\packs\Module',
        ],
        'menu' => [
            'class' => 'design\modules\menu\Module',
        ],
    ]
];