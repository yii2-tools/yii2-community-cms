<?php

/**
 * Low-level init operations (php-only environment)
 *
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 29.01.16 8:29
 */

defined('YII_APP_ID') or define('YII_APP_ID', 'yii2-community-cms');
defined('YII_APP_VERSION') or define('YII_APP_VERSION', '2.0.1');

require_once(__DIR__ . implode(DIRECTORY_SEPARATOR, ['', '..', '..', 'helpers', 'BaseHelper.php']));
require_once(__DIR__ . implode(DIRECTORY_SEPARATOR, ['', '..', '..', 'helpers', 'RouteHelper.php']));