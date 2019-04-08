<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 05.02.16 21:56
 */

return [
    'params' => $__params = require(__DIR__ . DIRECTORY_SEPARATOR . 'params.php'),
    'aliases' => [
        '@booting' => '@app/modules/booting',
    ],
    'components' => [
        'customizer' => [
            'class' => 'app\modules\booting\components\Customizer'
        ]
    ],
];