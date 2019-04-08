<?php

//defined('YII_DEBUG') or define('YII_DEBUG', true);
//defined('YII_ENV') or define('YII_ENV', 'test');

defined('YII_APP_BASE_PATH') or define('YII_APP_BASE_PATH', dirname(dirname(dirname(__DIR__))));

defined('YII_APP_WEB') or define('YII_APP_WEB', true);
defined('YII_APP_CONSOLE') or define('YII_APP_CONSOLE', false);
defined('YII_APP_PATH_CONFIG') or define('YII_APP_PATH_CONFIG', __DIR__ . implode(DIRECTORY_SEPARATOR, ['', '..', '..', '..', 'config']));

// dev
if (file_exists(YII_APP_PATH_CONFIG . DIRECTORY_SEPARATOR . 'ENV_DEV')) {
    defined('YII_ENV') or define('YII_ENV', 'dev');
}
// test
elseif (file_exists(YII_APP_PATH_CONFIG . DIRECTORY_SEPARATOR . 'ENV_TEST')) {
    defined('YII_ENV') or define('YII_ENV', 'test');
}
// prod
else {
    defined('YII_ENV') or define('YII_ENV', 'prod');
}

$appConfig = array_replace_recursive(
    require(YII_APP_PATH_CONFIG . implode(DIRECTORY_SEPARATOR, ['', 'default', 'console.php'])),
    require(YII_APP_PATH_CONFIG . implode(DIRECTORY_SEPARATOR, ['', YII_ENV, 'console.php']))
);

require($appConfig['vendorPath'] . DIRECTORY_SEPARATOR . 'autoload.php');
require($appConfig['vendorPath'] . implode(DIRECTORY_SEPARATOR, ['', 'yiisoft', 'yii2', 'Yii.php']));

$testsDir = dirname(dirname(__DIR__));
$appDir = dirname($testsDir);
Yii::setAlias('@tests', $testsDir);
Yii::setAlias('@webroot', $appDir . DIRECTORY_SEPARATOR . 'web');
Yii::setAlias('@web', $appDir);
Yii::setAlias('@migrations', $appDir . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'migrations');
