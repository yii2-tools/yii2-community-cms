<?php

/**
 * Application configuration for acceptance tests
 */

return array_replace_recursive(
    require(YII_APP_PATH_CONFIG . implode(DIRECTORY_SEPARATOR, ['', 'default', 'web.php'])),
    require(YII_APP_PATH_CONFIG . implode(DIRECTORY_SEPARATOR, ['', YII_ENV, 'web.php'])),
    require(__DIR__ . '/config.php'),
    [

    ]
);
