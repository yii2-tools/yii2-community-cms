<?php

defined('YII_APP_WEB') or define('YII_APP_WEB', true);
defined('YII_APP_CONSOLE') or define('YII_APP_CONSOLE', false);
defined('YII_APP_PATH_CONFIG') or define('YII_APP_PATH_CONFIG', __DIR__ . implode(DIRECTORY_SEPARATOR, ['', '..', '..', 'config']));

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
    require(YII_APP_PATH_CONFIG . implode(DIRECTORY_SEPARATOR, ['', 'default', 'web.php'])),
    require(YII_APP_PATH_CONFIG . implode(DIRECTORY_SEPARATOR, ['', YII_ENV, 'web.php']))
);

require_once($appConfig['vendorPath'] . DIRECTORY_SEPARATOR . 'autoload.php');
require_once($appConfig['vendorPath'] . implode(DIRECTORY_SEPARATOR, ['', 'yiisoft', 'yii2', 'Yii.php']));

if (!in_array(YII_ENV, [YII_ENV_DEV, YII_ENV_TEST])) {
    die('Testing available only for dev and test environments.');
}

$test_entry_url = \Codeception\Configuration::config()['config']['test_entry_url'];

defined('YII_TEST_ENTRY_URL') or define('YII_TEST_ENTRY_URL', parse_url($test_entry_url, PHP_URL_PATH));
defined('YII_TEST_ENTRY_FILE') or define('YII_TEST_ENTRY_FILE', dirname(dirname(__DIR__)) . '/web/index-test.php');

$_SERVER['SCRIPT_FILENAME'] = YII_TEST_ENTRY_FILE;
$_SERVER['SCRIPT_NAME'] = YII_TEST_ENTRY_URL;
$_SERVER['SERVER_NAME'] = parse_url($test_entry_url, PHP_URL_HOST);
$_SERVER['SERVER_PORT'] =  parse_url($test_entry_url, PHP_URL_PORT) ?: '80';

Yii::setAlias('@tests', dirname(__DIR__));
