<?php

$__params = require(__DIR__ . DIRECTORY_SEPARATOR . 'params.php');

defined('YII_PATH_APP_LIBS') or define('YII_PATH_APP_LIBS',
    $__params['yii2_community_cms_libs_pers'] . implode(DIRECTORY_SEPARATOR, ['', YII_APP_ID, YII_APP_VERSION]));

return [
    'params' => $__params,
    'vendorPath' => YII_PATH_APP_LIBS . DIRECTORY_SEPARATOR . 'vendor',
    'runtimePath' => $__params['yii2_community_cms_logs'] . implode(DIRECTORY_SEPARATOR, ['', YII_APP_ID, $__params['yii2_community_cms_site_domain'], 'runtime']),
    'components' => [
        'urlManager' => [
            'rules' => require(__DIR__ . DIRECTORY_SEPARATOR . 'routes.php')
        ],
        'session' => [
            'class' => 'yii\redis\Session',
            'keyPrefix' => ''//$__params['yii2_community_cms_site_id'],
        ],
        'log' => [
            'targets' => [
                'error' => [
                    'enabled' => true,
                ],
                'email' => [
                    'class' => 'yii\log\EmailTarget',
                    'enabled' => true,
                    'levels' => ['error'],
                    'except' => [
                        'yii\web\HttpException:400',
                        'yii\web\HttpException:403',
                        'yii\web\HttpException:404',
                        'yii\web\HttpException:405',
                    ],
                    'mailer' => [
                        'class' => 'yii\swiftmailer\Mailer',
                        'useFileTransport' => false,
                        'transport' => [
                            'class' => 'Swift_MailTransport',
                            'extraParams' => null,
                        ],
                    ],
                    'message' => [
                        'from' => $__params['yii2_community_cms_site_email_sender'],
                        'to' => $__params['yii2_community_cms_site_email_to_errors'],
                        'subject' => 'Error - ' . $__params['yii2_community_cms_site_domain'],
                    ],
                ],
            ],
        ],
        'cache' => [
            'class' => 'yii\redis\Cache',
            'keyPrefix' => ''//$__params['yii2_community_cms_site_id']
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@app/views/mail',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => $__params['yii2_community_cms_smtp_host'],
                'username' => $__params['yii2_community_cms_smtp_user'],
                'password' => $__params['yii2_community_cms_smtp_pass'],
                'port' => $__params['yii2_community_cms_smtp_port'],
                'encryption' => 'tls',
            ],
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'commandClass' => 'yii\tools\components\DbCommand',
            'dsn' => 'mysql:host=' . $__params['yii2_community_cms_site_db_host'] . ';dbname=' . $__params['yii2_community_cms_site_db_name'],
            'username' => $__params['yii2_community_cms_site_db_user'],
            'password' => $__params['yii2_community_cms_site_db_pass'],
            'charset' => 'utf8',
            'enableSchemaCache' => true,
        ],
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => $__params['yii2_community_cms_redis_host'],
            'port' => $__params['yii2_community_cms_redis_port'],
            'database' => $__params['yii2_community_cms_redis_db'],
        ],
        'assetManager' => [
            'assetMap' => require(__DIR__ . DIRECTORY_SEPARATOR . 'asset_map.php'),
            'bundles' => require(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . 'asset_bundles.php'),
        ]
    ],
];
