<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 23.01.16 13:48
 */

return [
    'params' => $__params = require(__DIR__ . DIRECTORY_SEPARATOR . 'params.php'),
    'id' => 'users',
    'aliases' => [
        '@users' => '@site/modules/users'
    ],
    'breadcrumbsExceptRoutes' => ['*'],
    'enableUnconfirmedLogin' => false,
    'components' => [
        'observer' => [
            'class' => 'site\modules\users\components\Observer',
        ],
    ],
];