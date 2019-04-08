<?php

require_once(__DIR__ . DIRECTORY_SEPARATOR . 'app.php');

defined('YII_PATH_APP_LIBS') or define('YII_PATH_APP_LIBS',
    dirname(__DIR__) . implode(DIRECTORY_SEPARATOR, ['', 'vendor']));

return [
    'params' => require(__DIR__ . DIRECTORY_SEPARATOR . 'params.php'),
    'bootstrap' => [
        10 => 'gii'
    ],
    'components' => [
        'log' => [
            'traceLevel' => 3,
            'targets' => [
                'app' => [
                    'enabled' => true
                ],
            ],
        ],
        'db' => require(__DIR__ . DIRECTORY_SEPARATOR . 'db.php')
    ],
    'modules' => [
        'gii' => 'yii\gii\Module',
    ],
];
