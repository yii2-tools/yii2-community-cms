<?php

if (file_exists('..' . DIRECTORY_SEPARATOR . 'LOCK')) {
    die('This site is temporarily unavailable...');
}

require_once 'env.php';

$config = array_replace_recursive(
    require(YII_APP_PATH_CONFIG . implode(DIRECTORY_SEPARATOR, ['', 'default', 'web.php'])),
    require(YII_APP_PATH_CONFIG . implode(DIRECTORY_SEPARATOR, ['', YII_ENV, 'web.php']))
);

require($config['vendorPath'] . DIRECTORY_SEPARATOR . 'autoload.php');
require($config['vendorPath'] . implode(DIRECTORY_SEPARATOR, ['', 'yiisoft', 'yii2', 'Yii.php']));

(new yii\web\Application($config))->run();
