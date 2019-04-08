<?php

/**
 * Application configuration shared by all test types
 */

return array_replace_recursive(
    require(__DIR__ . DIRECTORY_SEPARATOR . 'default.php'),
    [
        'components' => [
            'mailer' => [
                'useFileTransport' => true,
            ],
            'urlManager' => [
                'showScriptName' => true,
            ],
            'request' => [
                'baseUrl' => ''
            ]
        ],
    ]
);