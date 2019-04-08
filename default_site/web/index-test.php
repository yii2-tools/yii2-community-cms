<?php

// NOTE: Make sure this file is not accessible when deployed to production
if (!in_array(@$_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1', 'localhost'])) {
    die();
}

require_once 'env.php';

$appConfig = array_replace_recursive(
    require(YII_APP_PATH_CONFIG . implode(DIRECTORY_SEPARATOR, ['', 'default', 'web.php'])),
    require(YII_APP_PATH_CONFIG . implode(DIRECTORY_SEPARATOR, ['', YII_ENV, 'web.php']))
);

require_once($appConfig['vendorPath'] . DIRECTORY_SEPARATOR . 'autoload.php');
require_once($appConfig['vendorPath'] . implode(DIRECTORY_SEPARATOR, ['', 'yiisoft', 'yii2', 'Yii.php']));

$config = require(__DIR__ . '/../tests/codeception/config/acceptance.php');

require 'c3.php';

// fix: disable some modules dependent on REQUEST_URI & migrations during tests
//unset($config['bootstrap'][2]);
//unset($config['modules']['debug']);

(new yii\web\Application($config))->run();
