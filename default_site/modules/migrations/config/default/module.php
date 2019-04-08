<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 29.01.16 3:44
 */

return [
    'params' => $__params = require(__DIR__ . DIRECTORY_SEPARATOR . 'params.php'),
    'aliases' => [
        '@migrations' => '@app/modules/migrations',
    ],
    'migrationPaths' => [
        // yii2
        '@migrations/source/db/yii2/web',
        '@migrations/source/db/yii2/rbac',
        // yii2-community-cms
        '@migrations/source/db/yii2-community-cms/engine',
        '@migrations/source/db/yii2-community-cms/services',
        //'@migrations/source/db/yii2-community-cms/site',
        '@migrations/source/db/yii2-community-cms/design',
        '@migrations/source/files/yii2-community-cms/design',
        '@migrations/source/db/yii2-community-cms/users',
        '@migrations/source/db/yii2-community-cms/pages',
        '@migrations/source/db/yii2-community-cms/news',
        '@migrations/source/db/yii2-community-cms/plugins',
        '@migrations/source/db/yii2-community-cms/forum',
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
            'cachePath' => '@runtime/cache_migrations',
        ],
        'dbMigrator' => [
            'class' => 'app\modules\migrations\components\DbMigrator'
        ]
    ],
];