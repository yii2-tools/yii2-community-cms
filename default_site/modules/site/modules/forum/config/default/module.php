<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 21.03.2016 16:50
 * via Gii Module Generator
 */

return [
    'params' => $__params = require(__DIR__ . DIRECTORY_SEPARATOR . 'params.php'),
    'id' => 'forum',
    'aliases' => [
        '@forum' => '@site/modules/forum',
    ],
    'bootstrap' => [],
    'defaultRoute' => 'default/index',
    'components' => [
        'observer' => [
            'class' => 'site\modules\forum\components\Observer',
        ],
        'counter' => [
            'class' => 'site\modules\forum\components\Counter',
        ],
    ],
    'modules' => [

    ]
];