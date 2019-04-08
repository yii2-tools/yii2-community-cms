<?php
$_SERVER['SCRIPT_FILENAME'] = YII_TEST_ENTRY_FILE;
$_SERVER['SCRIPT_NAME'] = YII_TEST_ENTRY_URL;

/**
 * Application configuration for functional tests
 */
return yii\helpers\ArrayHelper::merge(
    require(YII_APP_PATH_CONFIG . implode(DIRECTORY_SEPARATOR, ['', 'default', 'web.php'])),
    require(YII_APP_PATH_CONFIG . implode(DIRECTORY_SEPARATOR, ['', YII_ENV, 'web.php'])),
    require(__DIR__ . '/config.php'),
    [
        'components' => [
            'request' => [
                // it's not recommended to run functional tests with CSRF validation enabled
                'enableCsrfValidation' => false,
                // but if you absolutely need it set cookie domain to localhost
                /*
                'csrfCookie' => [
                    'domain' => 'localhost',
                ],
                */
            ],
        ],
    ]
);
