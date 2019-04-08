<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 15.03.16 9:31
 */

return [
    'language' => 'en-US',
    'controllerMap' => [
        'fixture' => [
            'class' => 'yii\faker\FixtureController',
            'fixtureDataPath' => '@tests/codeception/fixtures',
            'templatePath' => '@tests/codeception/templates',
            'namespace' => 'tests\codeception\fixtures',
        ],
    ],
    'components' => [
        'db' => [
            'dsn' => 'mysql:host=localhost;dbname=yii2-community-cms',
        ],
    ],
];