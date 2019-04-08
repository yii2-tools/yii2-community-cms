<?php

require_once(__DIR__ . DIRECTORY_SEPARATOR . 'app.php');

defined('YII_PATH_APP_LIBS') or define('YII_PATH_APP_LIBS',
    dirname(__DIR__) . implode(DIRECTORY_SEPARATOR, ['', 'vendor']));

return [
    'params' => $__params = require(__DIR__ . DIRECTORY_SEPARATOR . 'params.php'),
    'bootstrap' => [
        2 => 'debug',
        3 => 'gii'
    ],
    'components' => [
        'urlManager' => [
            'rules' => require(__DIR__ . DIRECTORY_SEPARATOR . 'routes.php')
        ],
        'request' => [
            'baseUrl' => '/' . YII_APP_ID,
        ],
        'assetManager' => [
            'forceCopy' => true,
            //'assetMap' => require(__DIR__ . DIRECTORY_SEPARATOR . 'asset_map.php'),
            //'bundles' => require(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . 'asset_bundles.php'),
        ],
        'log' => [
            'traceLevel' => 3,
            'targets' => [
                'app' => [
                    'enabled' => true
                ],
            ],
        ],
        'db' => require(__DIR__ . DIRECTORY_SEPARATOR . 'db.php'),
    ],
    'modules' => [
        'debug' => [
            'class' => 'yii\debug\Module',
            'allowedIPs' => ['*'],
            'fileMode' => 0775,
        ],
        'gii' => [
            'class' => 'yii\gii\Module',
            'allowedIPs' => ['*'],
            'newFileMode' => 0775,
            'newDirMode' => 0775,
            'generators' => [
                'model' => [
                    'class' => 'app\modules\gii\generators\model\Generator',
                    'templates' => [
                        'default' => '@yii/gii/generators/model/default',
                        'model' => '@app/modules/gii/generators/model/templates'
                    ],
                ],
                'module' => [
                    'class' => 'app\modules\gii\generators\module\Generator',
                    'templates' => [
                        'default' => '@yii/gii/generators/module/default',
                        'module' => '@app/modules/gii/generators/module/templates'
                    ],
                ]
            ]
        ],
    ]
];