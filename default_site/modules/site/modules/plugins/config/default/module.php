<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 19.03.2016 13:43
 * via Gii Module Generator
 */

return [
    'params' => $__params = require(__DIR__ . DIRECTORY_SEPARATOR . 'params.php'),
    'id' => 'plugins',
    'aliases' => [
        '@plugins' => '@site/modules/plugins',
        '@plugins_dir' => '@companyName/plugins',
    ],
    'bootstrap' => [],
    'defaultRoute' => 'default/show',
    'components' => [
        'dataManager' => [
            'class' => 'site\modules\plugins\components\DataManager',
        ],
        'bundleManager' => [
            'class' => 'site\modules\plugins\components\BundleManager',
        ],
        'client' => [
            'class' => 'site\modules\plugins\components\Client',
        ],
    ],
    'modules' => [

    ]
];