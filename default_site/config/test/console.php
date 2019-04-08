<?php

$__params = require(__DIR__ . DIRECTORY_SEPARATOR . 'params.php');

defined('YII_DEBUG') or define('YII_DEBUG', true);

defined('YII_PATH_APP_LIBS') or define('YII_PATH_APP_LIBS',
    $__params['yii2_community_cms_libs_pers'] . implode(DIRECTORY_SEPARATOR, ['', YII_APP_ID, YII_APP_VERSION]));

return [
    'params' => $__params,
    'vendorPath' => YII_PATH_APP_LIBS . DIRECTORY_SEPARATOR . 'vendor',
    'runtimePath' => $__params['yii2_community_cms_logs'] . implode(DIRECTORY_SEPARATOR, ['', YII_APP_ID, $__params['yii2_community_cms_site_domain'], 'runtime']),
    'components' => [
        'log' => [
            'traceLevel' => 3,
            'targets' => [
                'app' => [
                    'enabled' => true
                ],
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
        ]
    ],
];
