<?php
/**
 * Application configuration for unit tests
 */
return yii\helpers\ArrayHelper::merge(
    require(YII_APP_PATH_CONFIG . implode(DIRECTORY_SEPARATOR, ['', 'default', 'web.php'])),
    require(YII_APP_PATH_CONFIG . implode(DIRECTORY_SEPARATOR, ['', YII_ENV, 'web.php'])),
    require(__DIR__ . '/config.php'),
    [

    ]
);
