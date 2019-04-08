<?php

require_once(__DIR__ . DIRECTORY_SEPARATOR . 'app.php');

return [
    'name' => 'Yii2 Community CMS',
    'id' => YII_APP_ID,
    'version' => YII_APP_VERSION,
    'params' => $__params = require(__DIR__ . DIRECTORY_SEPARATOR . 'params.php'),
    'basePath' => dirname(__DIR__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR,
    'vendorPath' => dirname(__DIR__) . DIRECTORY_SEPARATOR . '..' . implode(DIRECTORY_SEPARATOR, ['', 'vendor']),
    'runtimePath' => dirname(__DIR__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'runtime',
    // 0-99: system modules
    // 100-200: app engine modules
    'bootstrap' => [
        0 => 'log',
        10 => 'ci18n',
        20 => 'selftest',
        101 => 'routing',
        102 => 'services',
        200 => 'booting',
    ],
    'language' => 'ru-RU',
    'sourceLanguage' => 'en-US',
    'timeZone' => 'Europe/Moscow',
    // aliases for core modules
    'aliases' => [
        '@site' => '@app/modules/site',
        '@i18n' => '@site/modules/i18n',
        '@admin' => '@app/modules/admin',
        '@integrations' => '@app/modules/integrations',
        '@companyName' => '@integrations/modules/companyName',
        '@assets' => '@app/assets/dist',
    ],
    'defaultRoute' => 'engine/index',
    'components' => [

        /**
         * Dispatcher, setting up by app\modules\booting\Module in runtime
         * 'dispatcher' => []
         */

        /**
         * Loader, setting up by app\modules\booting\Module in runtime
         * 'loader' => []
         */

        /**
         * Router, setting up by app\modules\routing\Module in runtime
         * 'router' => []
         */

        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => require(__DIR__ . DIRECTORY_SEPARATOR . 'routes.php')
        ],
        'request' => [
            'class' => 'app\components\web\Request',
            'baseUrl' => '',
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'wlxlzzEZkW3Vu5TDbb3HrE27VAauzAKV',
        ],
        'view' => [
            'class' => $__params['default_class_view'],
        ],
        'session' => [
            'class' => $__params['default_class_session'],
        ],
        'authManager' => [
            'class' => 'admin\modules\users\components\DbManager',
            'cache' => 'cache',
        ],
        'assetManager' => [
            'appendTimestamp' => true,
            'hashCallback' => function ($path) {
                $path = (is_file($path) ? dirname($path) : $path) /*. filemtime($path)*/;
                return sprintf('%x', crc32($path . Yii::getVersion() . YII_APP_VERSION));
            },
        ],
        'cache' => [
            'class' => $__params['default_class_cache'],
            //'keyPrefix' => YII_APP_ID
        ],
        'errorHandler' => [
            'class' => 'app\components\web\ErrorHandler',
            'errorAction' => $__params['default_error_action'],
            'globalErrorCodes' => [400, 403],
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@app/views/mail',
            'useFileTransport' => true,
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
                'error' => [
                    'class' => 'yii\log\FileTarget',
                    'logFile' => '@runtime/logs/errors.log',
                    'except' => [
                        'yii\web\HttpException:400',
                        'yii\web\HttpException:403',
                        'yii\web\HttpException:404',
                        'yii\web\HttpException:405',
                    ],
                    'maxFileSize' => '4096',
                    'maxLogFiles' => '3',
                    'enabled' => !true,
                    'levels' => 1,
                ],
                'error_warning' => [
                    'class' => 'yii\log\FileTarget',
                    'logFile' => '@runtime/logs/errors_warnings.log',
                    'except' => [
                        'yii\web\HttpException:400',
                        'yii\web\HttpException:403',
                        'yii\web\HttpException:404',
                        'yii\web\HttpException:405',
                    ],
                    'maxFileSize' => '4096',
                    'maxLogFiles' => '3',
                    'enabled' => !true,
                    'levels' => 1 | 2,
                ],
                'migrations' => [
                    'class' => 'yii\log\FileTarget',
                    'logFile' => '@runtime/logs/migrations.log',
                    'maxFileSize' => '4096',
                    'maxLogFiles' => '3',
                    'enabled' => !true,
                    'levels' => 1 | 2 | 4 | 8,
                ],
            ],
        ],
        'db' => require(__DIR__ . DIRECTORY_SEPARATOR . 'db.php'),
        'formatter' => [
            'class' => 'app\components\Formatter'
        ],
    ],
    'modules' => [
        // проверка окружения в рантайме
        // например, если редис недоступен, в качестве кеша будет использоваться файловая система
        'selftest' => [
            'class' => 'app\modules\selftest\Module',
        ],
        'routing' => [
            'class' => 'app\modules\routing\Module',
        ],
        'booting' => [
            'class' => 'app\modules\booting\Module',
        ],
        // какие услуги в данных момент подключены к сайту
        // некоторый функционал сайта не будет работать, если с сервера услуг не прилетел флажок
        'services' => [
            'class' => 'app\modules\services\Module',
        ],
        'ci18n' => [
            'class' => 'app\modules\ci18n\Module',
        ],
        'migrations' => [
            'class' => 'app\modules\migrations\Module',
        ],
        'integrations' => [
            'class' => 'app\modules\integrations\Module'
        ],
        // sub-modules:
        // users, api, design, forum, i18n, news, pages, widgets, plugins, etc.
        'site' => [
            'class' => 'app\modules\site\Module',
        ],
        'admin' => [
            'class' => 'app\modules\admin\Module',
        ],
        'gridview' =>  [
            'class' => 'kartik\grid\Module'
        ]
    ]
];
