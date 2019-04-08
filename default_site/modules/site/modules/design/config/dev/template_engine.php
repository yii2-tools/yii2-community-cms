<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 07.03.16 14:40
 *
 * Twig engine configuration.
 *
 * WARNING: Don't remove whole config record, it can be used in code
 * WARNING: Don't implement lexerOptions or extensions configs here
 *  (instead, config some needed extensions in Booting Module)
 */

return [
    'options' => [
        'debug' => true,
    ],
    'sandboxOptions' => [
        // http://twig.sensiolabs.org/doc/functions/index.html
        'allowedFunctions' => [
            // twig
            100000 => 'dump',
        ],
    ],
    'extensions' => ['Twig_Extension_Debug'],
];
