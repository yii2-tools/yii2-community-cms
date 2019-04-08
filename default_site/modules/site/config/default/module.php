<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 23.01.16 13:48
 */

return [
    // Required module params.
    'params' => $__params = require(__DIR__ . DIRECTORY_SEPARATOR . 'params.php'),
    'id' => 'site',
    'bootstrap' => ['i18n', 'users', 'widgets', 'design', 'plugins'],
    // Core module params.
    'defaultRoute' => 'default/index',
    'breadcrumbs' => false,
    // Module components.
    'components' => [
        'captcha' => [
            'class' => 'site\components\Captcha',
        ]
    ],
    // Submodules.
    'modules' => [
        'i18n' => [
            'class' => 'site\modules\i18n\Module',
        ],
        'users' => [
            'class' => 'site\modules\users\Module',
        ],
        'design' => [
            'class' => 'site\modules\design\Module',
        ],
        'pages' => [
            'class' => 'site\modules\pages\Module',
        ],
        'news' => [
            'class' => 'site\modules\news\Module',
        ],
        'forum' => [
            'class' => 'site\modules\forum\Module',
        ],
        'api' => [
            'class' => 'site\modules\api\Module',
        ],
        'plugins' => [
            'class' => 'site\modules\plugins\Module',
        ],
        'widgets' => [
            'class' => 'site\modules\widgets\Module',
        ],
    ]
];