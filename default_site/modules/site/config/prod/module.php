<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 23.01.16 13:48
 */

return [
    'params' => $__params = require(__DIR__ . DIRECTORY_SEPARATOR . 'params.php'),
    'modules' => [
        'users' => [
            'mailer' => [
                'sender' => Yii::$app->params['yii2_community_cms_site_email_sender']
            ]
        ]
    ]
];