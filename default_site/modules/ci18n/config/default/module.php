<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 18.03.2016 19:45
 * via Gii Module Generator
 */

return [
    'params' => $__params = require(__DIR__ . DIRECTORY_SEPARATOR . 'params.php'),
    'id' => 'ci18n',
    'aliases' => [
        // WARNING: alias 'i18n' already used by 'site' module
        // 'site' module aliases have higher priority in app
        '@ci18n' => '@app/modules/ci18n'
    ],
    'bootstrap' => ['packs'],
    //'defaultRoute' => 'default/index',
    'components' => [

    ],
    'modules' => [
        'packs' => [
            'class' => 'ci18n\modules\packs\Module',
        ],
    ]
];