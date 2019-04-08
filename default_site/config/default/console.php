<?php

require_once(__DIR__ . DIRECTORY_SEPARATOR . 'app.php');

return [
    'id' => YII_APP_ID,
    'version' => YII_APP_VERSION,
    'params' => $__params = require(__DIR__ . DIRECTORY_SEPARATOR . 'params.php'),
    'basePath' => dirname(__DIR__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR,
    'vendorPath' => dirname(__DIR__) . DIRECTORY_SEPARATOR . '..' . implode(DIRECTORY_SEPARATOR, ['', 'vendor']),
    'runtimePath' => dirname(__DIR__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'runtime',
    'bootstrap' => [
        0 => 'log',
        1 => 'ci18n'
    ],
    'language' => 'ru-RU',
    'sourceLanguage' => 'en-US',
    'timeZone' => 'Europe/Moscow',
    'aliases' => [
        '@webroot' => '@app/web',
        '@web' => '/',
        '@site' => '@app/modules/site',
        '@design' => '@site/modules/design',
        '@design_packs_dir' => '@design/modules/packs/source',       // site/design/packs -> design_packs_dir
        '@i18n' => '@site/modules/i18n',
        '@admin' => '@app/modules/admin',
        '@migrations' => '@app/modules/migrations',
        '@integrations' => '@app/modules/integrations',
        '@companyName' => '@integrations/modules/companyName',
    ],
    'controllerNamespace' => 'app\commands',
    'controllerMap' => [
        'serve' => [
            'class' => 'app\controllers\ServeController'
        ]
    ],
    'components' => [
        'cache' => [
            'class' => $__params['default_class_cache'],
            //'keyPrefix' => YII_APP_ID
        ],
        'log' => [
            'traceLevel' => 0,
            'targets' => [
                'app' => [
                    'class' => 'yii\log\FileTarget',
                    'logFile' => '@runtime/logs/application.log',
                    'maxFileSize' => '4096',
                    'maxLogFiles' => '5',
                    'enabled' => !true,
                    'levels' => 1 | 2 | 4 | 8, // error, warning, info, trace
                ],
                'migrations' => [
                    'class' => 'yii\log\FileTarget',
                    'logFile' => '@runtime/logs/migrations.log',
                    'maxFileSize' => '4096',
                    'maxLogFiles' => '5',
                    'enabled' => !true,
                    'levels' => 1 | 2 | 4 | 8,
                ],
            ],
        ],
        'session' => [
            'class' => $__params['default_class_session'],
        ],
        'authManager' => [
            'class' => 'admin\modules\users\components\DbManager',
        ],
        'urlManager' => [
            'scriptUrl' => '/',
        ],
        'db' => require(__DIR__ . DIRECTORY_SEPARATOR . 'db.php'),
        'formatter' => [
            'class' => 'app\components\Formatter'
        ],
    ],
    'modules' => [
        'ci18n' => [
            'class' => 'app\modules\ci18n\Module'
        ],
        'migrations' => [
            'class' => 'app\modules\migrations\Module'
        ],
    ],
];
