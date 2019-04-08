<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 03.03.16 6:05
 */

defined('YII_APP_WEB') or define('YII_APP_WEB', true);
defined('YII_APP_CONSOLE') or define('YII_APP_CONSOLE', false);
defined('YII_APP_PATH_CONFIG') or define(
    'YII_APP_PATH_CONFIG',
    __DIR__ . implode(DIRECTORY_SEPARATOR, ['', '..', 'config'])
);

// dev
if (file_exists(YII_APP_PATH_CONFIG . DIRECTORY_SEPARATOR . 'ENV_DEV')) {
    defined('YII_ENV') or define('YII_ENV', 'dev');
} // test
elseif (file_exists(YII_APP_PATH_CONFIG . DIRECTORY_SEPARATOR . 'ENV_TEST')) {
    defined('YII_ENV') or define('YII_ENV', 'test');
} // prod
else {
    defined('YII_ENV') or define('YII_ENV', 'prod');
}
