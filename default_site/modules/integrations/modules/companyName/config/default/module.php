<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 21.02.2016 06:08
 * via Gii Module Generator
 */

return [
    'params' => $__params = require(__DIR__ . DIRECTORY_SEPARATOR . 'params.php'),
    'id' => 'companyName',
    'aliases' => [

    ],
    'bootstrap' => [],
    'defaultRoute' => 'default/index',
    'components' => [
        // Integrators (i.e. operation category).
        'plugins' => [
            'class' => 'integrations\modules\companyName\components\PluginsIntegrator',
            'context' => [
                'action' => ['*'],
                'queries' => ['*'],
                'plugin_key' => ['activate', 'update', 'deactivate'],
            ],
            'config' => [
                'action' => 'plugin_action',
                'queries' => [
                    'activate' => 1,
                    'update' => 2,
                    'deactivate' => 3,
                    'get' => 4,
                ]
            ]
        ],
        'widgets' => [
            'class' => 'integrations\modules\companyName\components\WidgetsIntegrator',
            'context' => [
                'action' => ['*'],
                'queries' => ['*'],
                'widget_key' => ['add', 'update', 'delete'],

            ],
            'config' => [
                'action' => 'widget_action',
                'queries' => [
                    'add' => 1,
                    'update' => 2,
                    'delete' => 3,
                    'get' => 4,
                ]
            ],
        ]
    ],
    'modules' => [

    ]
];