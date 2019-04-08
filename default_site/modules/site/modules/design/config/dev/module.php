<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 13.03.2016 08:01
 * via Gii Module Generator
 */

return [
    'params' => $__params = require(__DIR__ . DIRECTORY_SEPARATOR . 'params.php'),
    'components' => [
        'renderer' => require(__DIR__ . DIRECTORY_SEPARATOR . 'template_engine.php')
    ],
    'exceptRoutes' => ['gii'],
];