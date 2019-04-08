<?php

return [
    'class' => 'yii\db\Connection',
    'commandClass' => 'yii\tools\components\DbCommand',
    'dsn' => 'mysql:host=localhost;dbname=' . YII_APP_ID,
    'username' => 'root',
    'password' => 'root',
    'charset' => 'utf8',
];