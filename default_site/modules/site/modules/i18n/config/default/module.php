<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 18.03.2016 20:07
 * via Gii Module Generator
 */

return [
    'params' => $__params = require(__DIR__ . DIRECTORY_SEPARATOR . 'params.php'),
    'id' => 'i18n',
    'aliases' => [

    ],
    'bootstrap' => ['packs'],
    'defaultRoute' => 'default/index',
    'components' => [

    ],
    'modules' => [
        'packs' => [
            'class' => 'i18n\modules\packs\Module',
        ]
    ]
];