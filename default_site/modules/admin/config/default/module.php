<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 23.01.16 13:48
 */

return [
    // Required module params.
    'params' => $__params = require(__DIR__ . DIRECTORY_SEPARATOR . 'params.php'),
    'id' => 'admin',
    'bootstrap' => ['setup', 'users'],
    // Core module params.
    'defaultRoute' => 'default/index',
    // Module components.
    'components' => [

    ],
    // Submodules.
    'modules' => [
        'setup' => [
            'class' => 'admin\modules\setup\Module'
        ],
        'users' => [
            'class' => 'admin\modules\users\Module'
        ],
        'design' => [
            'class' => 'admin\modules\design\Module',
        ],
        'pages' => [
            'class' => 'admin\modules\pages\Module',
        ],
        'forum' => [
            'class' => 'admin\modules\forum\Module',
        ],
        'plugins' => [
            'class' => 'admin\modules\plugins\Module',
        ],
        'widgets' => [
            'class' => 'admin\modules\widgets\Module',
        ],
    ]
];
